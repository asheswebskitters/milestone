<?php  namespace LaravelAcl\Authentication\Controllers;
/**
 * Class PlansController
 *
 * @author @1712 Matrix Infologics Pvt Ltd
 */
use Illuminate\Http\Request;
use LaravelAcl\Http\Requests\StoreSlideRequest;
use LaravelAcl\Http\Requests\EditSlideRequest;
use LaravelAcl\Authentication\Models\Slide;
use LaravelAcl\Library\Exceptions\JacopoExceptionsInterface;
use View, Redirect, App, Config;

class SlidesController extends Controller
{
    /**
     * @var \LaravelAcl\Authentication\Models\Slide
     */
    protected $r;

    public function __construct()
    {
        $this->r = new Slide();
    }

    /**
    * To get the list of slides
    *
    * @author @1712 Matrix Infologics Pvt Ltd.
    * @param none
    * @return none
    */
    public function getList(){
        $objs = $this->r->paginate(Config::get('acl_base.items_per_page'));
        return View::make('laravel-authentication-acl::admin.slides.list')->with(["slides" => $objs]);
    }

    /**
    * To view of create slide
    *
    * @author @1712 Matrix Infologics Pvt Ltd.
    * @param none
    * @return none
    */

    public function createSlide()
    {
        $obj = new Slide;
        return View::make('laravel-authentication-acl::admin.slides.edit')->with(["slides" => $obj]);
    }

    /**
    * To save a new slide
    *
    * @author @1712 Matrix Infologics Pvt Ltd.
    * @param none
    * @return none
    */

    public function storeSlide(StoreSlideRequest $request)
    {
        $data = (object)$request->all();

        try
        {
            $obj = new Slide;
            $obj->title = $data->title;
            $obj->description = $data->description;
            $obj->image = $logo = Slide::uploadLogo($data);
            $obj->save();
        }

        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            // passing the id incase fails editing an already existing item
            return Redirect::route("slides.create", $id ? ["id" => $id]: [])->withInput()->withErrors($errors);
        }

        return Redirect::route("slides.index")->withMessage('Slide successfully created');
    }

    /**
    * To Edit the slide
    *
    * @author @1712 Matrix Infologics Pvt Ltd.
    * @param $request
    * @return none
    */
    public function editSlide(Request $request)
    {
        try
        {
            $obj = $this->r->find($request->get('id'));
        }
        catch(JacopoExceptionsInterface $e)
        {
            $obj = new Slide;
        }
        return View::make('laravel-authentication-acl::admin.slides.edit')->with(["slides" => $obj]);
    }

    /**
    * To update plan
    *
    * @author @1712 Matrix Infologics Pvt Ltd.
    * @param $request
    * @return none
    */
    public function postEditSlide(EditSlideRequest $request)
    {
        $id = $request->get('id');
        $data = (object)$request->all();
        try
        {
            $obj = $this->r->find($id);
            $obj->title = $data->title;
            $obj->description = $data->description;
            if(isset($data->image) && is_object($data->image)){
                $data->image = Slide::uploadLogo($data);
            }
            $obj->save();
        }

        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            // passing the id incase fails editing an already existing item
            return Redirect::route("slides.edit", $id ? ["id" => $id]: [])->withInput()->withErrors($errors);
        }

        return Redirect::route("slides.index")->withMessage('Slide successfully updated');
    }

    /**
    * To delete a slide
    *
    * @author @1712 Matrix Infologics Pvt Ltd.
    * @param $request
    * @return none
    */
    public function deleteSlide(Request $request)
    {
        try
        {
            $obj = $this->r->find($request->get('id'));
            $obj->delete();
        }
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            return Redirect::route('slides.index')->withErrors($errors);
        }
        return Redirect::route('slides.index')->withMessage('Slide Successfully deleted');
    }
} 