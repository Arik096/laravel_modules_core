@extends('core::layouts.app')
@section('title','Users Create')
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
    {{Form::open(['route'=>['core.users.store',$prefix],'method'=>'POST','files'=>true])}}

    <div class="isocial-body-section box-bottom-shadow">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-6">
                    <nav aria-label="breadcrumb" style="margin-top: 17px;">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('core.users.index',$prefix)}}" class="text-d-none"><b>Users</b></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create Users</li>
                        </ol>
                    </nav>
                </div>

                <div class="col-md-6 text-end" style="margin-top: 12px; margin-bottom: 13px;">
                    <button type="submit" class="btn-create-module"><i class="fa fa-save"></i> Save</button>
                    <a href="{{route('core.users.index',$prefix)}}" class="discard-button"><b>DISCARD</b></a>
                </div>

            </div>
        </div>
    </div>



    <div class="container-xxl" style="margin-top: 20px;">
        <section class="container-xxl isocial-body-section section-table-border-custom" style="border-top: none; border-bottom: none; padding-right: 0px; padding-left: 0px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-7 offset-md-2" style="margin-top: 40px; padding-left: 40px;">

                        <div class="col-md-12">

                            <div class="row">
                                <div class="col-6">
                                    <label class="form-label">User Name <span style="color: silver">(English)</span></label>
                                    <input type="text" name="name_en" class="form-control i-f-d" placeholder="Write your name">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">User Name <span style="color: silver">(Bangla)</span></label>
                                    <input type="text" name="name_bn" class="form-control i-f-d" placeholder="Write your name">
                                </div>

                            </div>


                            <div class="mb-5" style="margin-top: 30px;">
                                <label class="form-label">Roles</label>
                                <select name="role_id"  class="form-select i-f-d js-example-basic-single">
                                    <option value="">Choose</option>
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                                <div class="mb-5">
                                    <label class="form-label">Mobile</label>
                                    <input type="text" name="mobile" class="form-control i-f-d" placeholder="Write your mobile number">
                                </div>


                                <div class="mb-5">
                                    <label class="form-label">Email</label>
                                    <input type="text" name="email" class="form-control i-f-d" placeholder="Write your email">
                                </div>


                                <div class="mb-5">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control i-f-d" placeholder="Enter your password">
                                </div>


                        </div>

                    </div>
                    <div class="col-6" style="margin-top: 40px; padding-right: 40px;">



                    </div>
                </div>
            </div>

            <div class="col-md-1" style="margin-top: 30px;"></div>
            <div class="col-md-6" style="margin-top: 30px;"></div>

        </section>
    </div>


    {{Form::close()}}

    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });

        $('#icon').change(function () {
            $("#changeImageDesktop").attr('src',window.URL.createObjectURL(this.files[0]))
        });
    </script>
@endsection
