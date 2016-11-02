<?php  
namespace LaravelAcl\Authentication\Models;
use Illuminate\Support\Facades\Input;
use LaravelAcl\Authentication\Classes\Uploader;
/**
 * Class Slide
 *
 * @author @1712 Matrix infologics Pvt Ltd
 */

class Slide extends BaseModel
{
    protected $table = "slides";

    protected $fillable = ["title","image","description"];

    protected $guarded = ["id"];


    public static function uploadLogo($data){

		$logo='';
		if(isset($data->image) && is_object($data->image)){
	 		$destinationPath = public_path().'/uploads/slides/';
			$uploader = new Uploader($destinationPath, $data->image);
			$logo = $uploader->upload();
		}
		return $logo;
	}


} 