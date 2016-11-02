<div class="row">
    <div class="col-md-12 margin-bottom-12">
        <a href="{!! URL::route('slides.create') !!}" class="btn btn-info pull-right" title="Add New Slide"><i class="fa fa-plus"></i> Add New</a>
    </div>
</div>
@if( !$slides->isEmpty() )
    <table class="table table-hover table-bordered gray_th">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Image</th>
            <th class="text-center">Operations</th>
        </tr>
        </thead>
        <tbody>
            @foreach($slides as $slide)
            <tr>                
                <td style="width:10%">{!! $slide->id !!}</td>
                <td style="width:15%">{!! $slide->title !!}</td>
                <td style="width:12%"><img src="{!! asset('uploads/slides').'/'.$slide->image !!}" width="100px" /></td>
                <td style="witdh:10%" class="text-center">
                    @if(! $slide->protected)
                    <a href="{!! URL::route('slides.edit', ['id' => $slide->id]) !!}" title="Edit slide"><i class="fa fa-pencil-square-o fa-2x"></i></a>
                    <a href="{!! URL::route('slides.delete',['id' => $slide->id, '_token' => csrf_token()]) !!}" title="Delete slide"class="margin-left-5"><i class="fa fa-trash-o delete fa-2x"></i></a>
                    @else
                        <i class="fa fa-times fa-2x light-blue"></i>
                        <i class="fa fa-times fa-2x margin-left-12 light-blue"></i>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {!! $slides->render() !!}
@else
<span class="text-warning"><h5>No slides found.</h5></span>
@endif