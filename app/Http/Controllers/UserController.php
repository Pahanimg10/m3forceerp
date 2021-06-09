<?php

namespace App\Http\Controllers;

require_once 'ESMSWS.php';
session_start();
date_default_timezone_set('Asia/Colombo');
set_time_limit(0);

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_access');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! session()->get('LoggedIn') || (! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')))) {
            return redirect('/home');
        } else {
            $data['main_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
                ->whereIn('user_access.user_group_id', session()->get('user_group'))
                ->where('side_menu.menu_category', 0)
                ->orderBy('side_menu.menu_order', 'asc')
                ->distinct('side_menu.id')
                ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
                ->get();
            $data['sub_menus'] = \App\Model\UserAccess::leftJoin('side_menu', 'side_menu.id', '=', 'user_access.side_menu_id')
                ->whereIn('user_access.user_group_id', session()->get('user_group'))
                ->where('side_menu.menu_category', '!=', 0)
                ->orderBy('side_menu.menu_order', 'asc')
                ->distinct('side_menu.id')
                ->select('side_menu.id as id', 'side_menu.menu_order as menu_order', 'side_menu.menu_category as menu_category', 'side_menu.menu_name as menu_name', 'side_menu.menu_id as menu_id', 'side_menu.menu_icon as menu_icon', 'side_menu.menu_url as menu_url')
                ->get();

            return view('admin.manage_user', $data);
        }
    }

    public function users_list()
    {
        $users = \App\Model\User::select('id', 'job_position_id', 'first_name', 'last_name', 'contact_no', 'email', 'username', 'user_image')
            ->where('is_delete', 0)
            ->with(['JobPosition' => function ($query) {
                $query->select('id', 'name');
            }])
            ->get();
        $result = [
            'users' => $users,
            'login_id' => session()->get('users_id'),
        ];

        return response($result);
    }

    public function find_user(Request $request)
    {
        $users = \App\Model\User::select('id', 'job_position_id', 'first_name', 'last_name', 'contact_no', 'email', 'username', 'user_image')
            ->with(['JobPosition' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['UserGroupPermission' => function ($query) {
                $query->select('id', 'user_id', 'user_group_id');
            }])
            ->find($request->id);

        return response($users);
    }

    public function job_positions_list()
    {
        $job_positions = \App\Model\JobPosition::select('id', 'name')
            ->where('is_delete', 0)
            ->orderBy('name')
            ->get();

        return response($job_positions);
    }

    public function group_list()
    {
        $groups = \App\Model\UserGroup::select('id', 'name', 'permission')
            ->orderBy('id', 'desc')
            ->get();

        return response($groups);
    }

    public function validate_username(Request $request)
    {
        if ($request->old_value != $request->username) {
            $user = \App\Model\User::where('username', $request->username)
                ->where('is_delete', 0)
                ->first();
            if ($user) {
                $result = 'false';
            } else {
                $result = 'true';
            }
        } else {
            $result = 'true';
        }

        echo $result;
    }

    public function image_upload()
    {
        if (! empty($_FILES['image'])) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = time().'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'assets/images/users/'.$image);

            $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/user_controller.csv', 'a+') or die('Unable to open/create file!');
            fwrite($myfile, 'Image Uploaded,,,,,,,assets/images/users/'.str_replace(',', ' ', $image).',,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
            fclose($myfile);

            $result = [
                'response' => true,
                'message' => 'success',
                'image' => $image,
            ];
        } else {
            $result = [
                'response' => false,
                'message' => 'Image Is Empty',
            ];
        }

        echo json_encode($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! session()->get('LoggedIn') || (! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')))) {
            return redirect('/home');
        } else {
            $user = new \App\Model\User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->contact_no = $request->contact_no;
            $user->email = $request->email;
            $user->job_position_id = $request->job_position['id'];
            $user->user_image = $request->image;
            $user->username = $request->username;
            $user->password = md5(sha1($request->password));

            if ($user->save()) {
                $types = $request->types;
                $user_group_ids = '';
                for ($i = 0; $i < count($types); $i++) {
                    if ($types[$i]['selected']) {
                        $permission = new \App\Model\UserGroupPermission();
                        $permission->user_id = $user->id;
                        $permission->user_group_id = $types[$i]['id'];
                        $permission->save();

                        $user_group_ids .= $user_group_ids != '' ? '|'.$permission->user_group_id : $permission->user_group_id;
                    }
                }

                $data = [
                    'name' => $request->first_name.' '.$request->last_name,
                    'email' => $request->email,
                    'username' => $request->username,
                    'password' => $request->password,
                ];

                Mail::send('emails.login_details', $data, function ($message) use ($data) {
                    $message->from('mail.smtp.m3force@gmail.com', 'M3Force ERP System');
                    $message->to($data['email'], $data['name']);
                    $message->cc('ruween@m3force.com', 'Ruween Dinesh Nugawela  | Software Engineer');
                    $message->subject('M3Force ERP Login Details');
                });

                if ($request->contact_no) {
                    $sms = '--- M3FORCE ERP Login ---'.PHP_EOL;
                    $sms .= 'Name : '.$request->first_name.' '.$request->last_name.PHP_EOL;
                    $sms .= 'Username : '.$request->username.PHP_EOL;
                    $sms .= 'Password : '.$request->password;

                    $session = createSession('', 'esmsusr_1na2', '3p4lfqe', '');
                    sendMessages($session, 'M3FORCE', $sms, [$request->contact_no]);
                }

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/user_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Created,'.$user->id.','.str_replace(',', ' ', $user->first_name).','.str_replace(',', ' ', $user->last_name).','.str_replace(',', ' ', $user->contact_no).','.str_replace(',', ' ', $user->email).','.$user->job_position_id.','.str_replace(',', ' ', $user->user_image).','.str_replace(',', ' ', $user->username).','.str_replace(',', ' ', $user->password).','.$user_group_ids.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $result = [
                    'response' => true,
                    'message' => 'Profile created successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Profile creation failed',
                ];
            }

            echo json_encode($result);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! session()->get('LoggedIn') || (! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')))) {
            return redirect('/home');
        } else {
            $user = \App\Model\User::find($id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->contact_no = $request->contact_no;
            $user->email = $request->email;
            $user->job_position_id = $request->job_position['id'];
            $user->user_image = $request->image;
            $user->username = $request->username;
            $user->password = md5(sha1($request->password));

            if ($user->save()) {
                $types = $request->types;
                for ($i = 0; $i < count($types); $i++) {
                    $permission = \App\Model\UserGroupPermission::where('user_id', $user->id)
                        ->where('user_group_id', $types[$i]['id'])
                        ->where('is_delete', 0)
                        ->first();
                    if ($permission) {
                        $permission->is_delete = 1;
                        $permission->save();
                    }
                }

                $user_group_ids = '';
                for ($i = 0; $i < count($types); $i++) {
                    if ($types[$i]['selected']) {
                        $permission = \App\Model\UserGroupPermission::where('user_id', $user->id)
                            ->where('user_group_id', $types[$i]['id'])
                            ->first();
                        if ($permission) {
                            $permission->is_delete = 0;
                            $permission->save();
                        } else {
                            $new_permission = new \App\Model\UserGroupPermission();
                            $new_permission->user_id = $user->id;
                            $new_permission->user_group_id = $types[$i]['id'];
                            $new_permission->save();
                        }
                        $user_group_ids .= $user_group_ids != '' ? '|'.$types[$i]['id'] : $types[$i]['id'];
                    }
                }

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/user_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Updated,'.$user->id.','.str_replace(',', ' ', $user->first_name).','.str_replace(',', ' ', $user->last_name).','.str_replace(',', ' ', $user->contact_no).','.str_replace(',', ' ', $user->email).','.$user->job_position_id.','.str_replace(',', ' ', $user->user_image).','.str_replace(',', ' ', $user->username).','.str_replace(',', ' ', $user->password).','.$user_group_ids.','.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $result = [
                    'response' => true,
                    'message' => 'Profile updated successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Profile updation failed',
                ];
            }

            echo json_encode($result);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! session()->get('LoggedIn') || (! in_array(1, session()->get('user_group')) && ! in_array(2, session()->get('user_group')))) {
            return redirect('/home');
        } else {
            $user = \App\Model\User::find($id);
            $user->is_delete = 1;

            if ($user->save()) {
                $permissions = \App\Model\UserGroupPermission::where('user_id', $user->id)
                    ->where('is_delete', 0)
                    ->get();
                foreach ($permissions as $permission) {
                    $permission->is_delete = 1;
                    $permission->save();
                }

                $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/m3force/public/assets/system_logs/user_controller.csv', 'a+') or die('Unable to open/create file!');
                fwrite($myfile, 'Deleted,'.$user->id.',,,,,,,,,,'.date('Y-m-d H:i:s').','.session()->get('users_id').','.str_replace(',', ' ', session()->get('username')).PHP_EOL);
                fclose($myfile);

                $result = [
                    'response' => true,
                    'message' => 'Profile deleted successfully',
                ];
            } else {
                $result = [
                    'response' => false,
                    'message' => 'Profile deletion failed',
                ];
            }

            echo json_encode($result);
        }
    }
}
