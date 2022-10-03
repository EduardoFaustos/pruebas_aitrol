<?php



namespace App\Http\Controllers;



use App\Models\Configuration;
use App\Models\LicensesUser;
use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;



class DashboardController extends Controller

{

    // Dashboard - Analytics

    public function dashboardAnalytics()

    {

        $pageConfigs = ['pageHeader' => false];



        return view('/content/dashboard/dashboard-analytics', ['pageConfigs' => $pageConfigs]);
    }



    // Dashboard - Ecommerce

    public function dashboardEcommerce()

    {

        $pageConfigs = ['pageHeader' => false];

        if (Auth::check()) {

            return view('/content/dashboard/dashboard-ecommerce', ['pageConfigs' => $pageConfigs]);
        } else {

            return redirect()->route('login');
        }

        return view('/content/dashboard/dashboard-ecommerce', ['pageConfigs' => $pageConfigs]);
    }

    public function darkmode(Request $request)

    {

        $icon = "feather-sun";

        if ($request->class == "light-layout") {
        } else {

            $icon = "feather-moon";
        }

        $iduser = Auth::user()->id;

        $check = Configuration::where('id_user', $iduser)->first();

        if (!is_null($check)) {

            $check->class = $request->class;

            $check->save();
        } else {

            Configuration::create([

                'class' => $request->class,

                'icon' => $icon,

                'id_user' => $iduser

            ]);
        }

        return response()->json(['state' => 'success']);
    }

    public function index(Request $request)

    {

        $pageConfigs = ['showMenu' => false];

        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "Layouts"], ['name' => ""]];

        if (Auth::check()) {

            return redirect()->route('search');
        }

        return view('welcome', ['pageConfigs' => $pageConfigs]);
    }

    public function search(Request $request)

    {

        $pageConfigs = ['showMenu' => false,   'pageClass' => 'chat-application'];

        if (Auth::check()) {

            $pageConfigs = ['showMenu' => false, 'pageClass' => 'chat-application'];
        }

            //dd($request->all());
            /*     "status" => "on"
        "minpriceh" => "1"
        "maxpriceh" => "300.00"
        "minpricef" => "1"
        "maxpricef" => "300.00"
        "minactivity" => "1"
        "maxactivity" => "300.00"
        "minperformace" => "1"
        "maxperformace" => "300.00"
        "mindistance" => "1"
        "maxdistance" => "300.00"
        "ubication" => null
        "search" => "aaaa"
        "userStatus" => "online" */
        $query = User::where('state', '1');
        if (!is_null($query)) {
            if(isset($request['checkstatus'])){
                if (!is_null($request['search'])) {
                    $query = $query->where('name', 'like', '%' . $request['search'] . '%')->orwhere('keywords','like', '%' . $request['search'] . '%');
                   
                    
                }
                if(isset($request['checkstatus']['online'])){
                    $on=1;
                    if($request->status=="on"){
                        $on=1;
                    }else{
                        $on=0;
                    }
                    $query= $query->where('status',$on);
                }
                if(isset($request['checkstatus']["'ph'"])){
                    $query= $query->whereBetween('price',[$request->minpriceh,$request->maxpriceh]);
                }
                if(isset($request['checkstatus']["'pf'"])){
                    $query= $query->whereBetween('price2',[$request->minpricef,$request->maxpricef]);
                }
                if(isset($request['checkstatus']["'u'"])){
                    $query= $query->where('city',$request->ubication);
                }
            }else{
                if (!is_null($request['search'])) {
                    $query = $query->where('name', 'like', '%' . $request['search'] . '%')->orwhere('presentjob', 'like', '%' . $request['search'] . '%')->orwhere('aboutme', 'like', '%' . $request['search'] . '%')->orwhere('activity', 'like', '%' . $request['search'] . '%')->orwhere('keywords','like', '%' . $request['search'] . '%');
                }
            }
            
        }

        $query = $query->get();

        return view('list', ['pageConfigs' => $pageConfigs, 'query' => $query, 'searchvalue' => $request['search'],'values'=>$request->all()]);
    }

    public function profile($id)

    {

        $user = User::find($id);

        $pageConfigs = ['showMenu' => false];

        if (Auth::check()) {

            $pageConfigs = ['showMenu' => false];
        }
        $image = ['jpg', 'png', 'gift'];
        $pdfile = ['pdf', 'doc'];
        $archivesi = LicensesUser::where('id_user', $user->id)->whereIn('file', $image)->get();
        $archivespdf = LicensesUser::where('id_user', $user->id)->whereIn('file', $pdfile)->get();

        return view('profile', ['user' => $user, 'pageConfigs' => $pageConfigs, 'archivesi' => $archivesi, 'archivespdf' => $archivespdf]);
    }
    public function autocomplete(Request $request)
    {
        /*     foreach($result as $row)
    {
     $temp_array = array();
     $temp_array['value'] = $row['student_name'];
     $temp_array['label'] = '<img src="images/'.$row['image'].'" width="70" />&nbsp;&nbsp;&nbsp;'.$row['student_name'].'';
     $output[] = $temp_array;
    } */
        $parameter = $request->term;
        $data = [];
        $data = User::where('state', '1');
        if (!is_null($parameter)) {
            $data = $data->where('name', 'like', '%' . $parameter . '%')->orwhere('presentjob', 'like', '%' . $parameter . '%')->orwhere('aboutme', 'like', '%' . $parameter . '%')->orwhere('activity', 'like', '%' . $parameter . '%');
        }
        $data = $data->get();
        $arry = [];
        foreach ($data as $data) {
            $m['value'] = $data->name;
            $m['name'] = $data->name;
            $m['label'] = '<img src="' . asset('users/' . $data->url_image) . '" width="70" />&nbsp;&nbsp;&nbsp;' . $data->name . '';
            array_push($arry, $m);
        }
        return json_encode($arry);
    }
}
