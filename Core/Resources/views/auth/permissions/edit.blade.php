@extends('core::layouts.app')
@section('title','Edit Create')
@section('top_script')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            font-size: 16px !important;
            color: #666666 !important;
        }
    </style>
@endsection

@section('content')

    {{Form::open(['route'=>['core.permissions.update',$prefix,$data->id],'method'=>'PUT','files'=>true])}}
    <div class="isocial-body-section box-bottom-shadow">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-6">
                    <nav aria-label="breadcrumb" style="margin-top: 17px;">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('core.permissions.index',$prefix)}}" class="text-d-none"><b>Permissions</b></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Permission</li>
                        </ol>
                    </nav>
                </div>

                <div class="col-md-6 text-end" style="margin-top: 12px; margin-bottom: 13px;">
                    <button type="submit" class="btn-create-module"><i class="fa fa-save"></i> Save</button>
                    <a href="{{route('core.permissions.index',$prefix)}}" class="discard-button"><b>DISCARD</b></a>
                </div>

            </div>
        </div>
    </div>





    <div class="container-xxl" style="margin-top: 20px;">
        <section class="container-xxl isocial-body-section section-table-border-custom" style="border-top: none; border-bottom: none; padding-right: 0px; padding-left: 0px;">
            <div class="container">
                <div class="row" style="padding-bottom: 50px;">
                    <div class="col" style="margin-top: 40px; padding-left: 40px;">

                        <div class="col-md-9">

                            <div class="mb-5">
                                <label class="form-label">Permission Name</label>
                                <input type="text" name="name" value="{{$data->name}}" class="form-control i-f-d" placeholder="Write your module name">
                            </div>
                            @livewire('core::permission.edit',['data'=>$data])

                            <div class="mb-5">
                                <label class="form-label">Action</label>
                                <input type="text" name="action" class="form-control i-f-d" value="{{$data->action}}" placeholder="Write your Action name">
                                <span style="color:silver;font-size: 12px;">Ex: \Modules\Product\Http\Controllers\Auth\ProductController@index</span>
                            </div>

                        </div>

                    </div>
                    <div class="col" style="margin-top: 40px; padding-right: 40px;">

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="exampleFormControlTextarea1" class="form-label">Write Details about this Permission</label>
                                <textarea name="comments" class="form-control i-f-d- color-gray" id="exampleFormControlTextarea1" rows="10">{{$data->comments}}</textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-1" style="margin-top: 30px;"></div>
            <div class="col-md-6" style="margin-top: 30px;"></div>

        </section>
    </div>


    {{Form::close()}}

@endsection

@section('bottom_script')
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
@endsection
