@extends('laravel-authentication-acl::admin.layouts.base-2cols')

@section('title')
Admin area: edit Slide
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
        {{-- model general errors from the form --}}
        @if($errors->has('model') )
        <div class="alert alert-danger">{{$errors->first('model')}}</div>
        @endif

        {{-- successful message --}}
        <?php $message = Session::get('message'); ?>
        @if( isset($message) )
        <div class="alert alert-success">{{$message}}</div>
        @endif
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title bariol-thin">{!! isset($slides->id) ? '<i class="fa fa-pencil"></i> Edit' : '<i class="fa fa-lock"></i> Create' !!} Plan</h3>
            </div>
            <div class="panel-body">
                @if(isset($slides->id))
                    {!! Form::model($slides, [ 'url' => [URL::route('slides.edit'), $slides->id], 'method' => 'post', 'files' => true] )  !!}
                @else
                    {!! Form::model($slides, [ 'url' => [URL::route('slides.create'), $slides->id], 'method' => 'post', 'files' => true] )  !!}
                @endif
              
				<div class="row">		
					<div class="col-md-6">
						<!-- name text field -->
						<div class="form-group">
							{!! Form::label('title','Title: *') !!}
							{!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Name', 'id' => 'name']) !!}
							 <span class="text-danger">{!! $errors->first('title') !!}</span>
						<!-- type select field -->
						</div>
					</div>
				</div>
               
             	<div class="row">		
					<div class="col-md-12">				
						<div class="form-group">
							{!! Form::label('image','Image: *') !!}
							{!! Form::file('image', null, ['class' => 'form-control ', 'placeholder' => 'image', 'id' => 'description']) !!}
							 <span class="text-danger">{!! $errors->first('image') !!}</span>
						</div>
						@if(isset($slides->id))
							@if(!empty($slides->image))
								<img src="{!! asset('uploads/slides').'/'.$slides->image !!}" height="100" />
							@endif
						@endif
					</div>
				</div>
				
				<div class="row">		
					<div class="col-md-12">				
						<div class="form-group">
							{!! Form::label('description','Description: *') !!}
							{!! Form::textarea('description', null, ['class' => 'form-control editor', 'placeholder' => 'Description', 'id' => 'description']) !!}
							 <span class="text-danger">{!! $errors->first('description') !!}</span>
						</div>
					</div>
				</div>
				
							   

				<div class="row">
					<div class="col-md-12">	
						{!! Form::hidden('id') !!}             
						{!! Form::submit('Save', array("class"=>"btn btn-info pull-right ")) !!}
					</div>	
				</div>	
					{!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@stop

@section('footer_scripts')
{!! HTML::script('packages/jacopo/laravel-authentication-acl/js/vendor/slugit.js') !!}
<script>
    $(".delete").click(function(){
        return confirm("Are you sure to delete this item?");
    });
    $(function(){
        $('#slugme').slugIt();
    });
</script>
@stop