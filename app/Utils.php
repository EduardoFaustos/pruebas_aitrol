<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utils
{

    /*   protected $table = 'suggestion';

   /**
   * The attributes that aren't mass assignable.
   *
   * @var array
   
   protected $guarded = []; */
    public static function getFollowers($id,$reference="", $type = "")
    {
        $suggestion = Suggestion::where('id_user2', $id)->where('state',1)->get()->count();
        if ($type == "I") {
            $suggestion = Suggestion::where('id_user', $id)->where('state',1)->get()->count();
        }else if ($type=="V"){
            $suggestion = Suggestion::where('id_user',$reference)->where('id_user2', $id)->where('state',1)->get()->count();
        }
        return $suggestion;
    }
    public static function getFavorites($id,$reference="", $type = "")
    {
        $favorites = Favorites::where('id_reference', $id)->where('state',1)->get()->count();
        if ($type == "I") {
            $favorites = Favorites::where('id_user', $id)->where('state',1)->get()->count();
        }else if ($type=="V"){
            $favorites = Favorites::where('id_user',$reference)->where('id_reference', $id)->where('state',1)->get()->count();
        }
        return $favorites;
    }
    public static function getFans($id,$reference="", $type = "")
    {
        $fan = Fans::where('id_reference', $id)->where('state',1)->get()->count();
        if ($type == "I") {
            $fan = Fans::where('id_user', $id)->where('state',1)->get()->count();
        }else if ($type=="V"){
            $fan = Fans::where('id_user',$reference)->where('id_reference', $id)->where('state',1)->get()->count();
        }
        return $fan;
    }
    public static function groupBy($data = [], $key = "")
    {
        $result = array();
        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[""][] = $val;
            }
        }

        return $result;
    }
    public static function validateChat($reference,$id){
        $validate=0;
        $suggestion = Suggestion::where('id_user',$reference)->where('id_user2', $id)->where('state',1)->first();
        if($suggestion!=null){
            $validate=1;
        }
        return $validate;
    }
}
