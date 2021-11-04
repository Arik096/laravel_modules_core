@extends('core::layouts.app')
@section('title','Users List')
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

    <div class="form-top-card">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <p style="margin: 10px 0px;font-size: 14px;">
                        <span style="font-size: 16px;color: #8E73BE"> <i class="fas fa-sliders-h"></i> Users List</span>
                        <a href="{{route('core.users.create',$prefix)}}"> <button class="btn-create-module float-right"><i class="fa fa-plus"></i> Create</button></a>
                    </p>
                </div>
            </div>
        </div>
    </div>



    <div class="container-xxl" style="margin-top: 20px;">
        <section class="container-xxl isocial-body-section section-table-border-custom" style="border-top: none; border-bottom: none; padding-right: 0px; padding-left: 0px;">

            <div class="row g-0 m">
                <div class="col-md-10 m-t-20">
                    {{Form::open(['route'=>['core.users.filter',$prefix],'method'=>'GET'])}}
                    <div class="row" style="padding: 0px 20px;box-sizing: border-box">
                        <div class="col-md-5">
                            <input name="search" type="text" class="search" placeholder="Search">
                        </div>

                        <div class="col-md-5">
                            <select name="role_id" class="form-control js-example-basic-single isocial-form-design">
                                <option value="">Choose</option>
                                @foreach($roles as $role)
                                    <option value="{{$role->id}}">{{$role->name}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-2">
                            <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filter</button>
                        </div>
                    </div>

                    {{Form::close()}}
                </div>

                <div class="col-md-2 m-t-20">
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn-export p-t-3 p-b-3"><i class="fa fa-file-import"></i> &nbsp; Export</button> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    </div>
                    <div class="col-md-12 text-end m-t-10 m-b-mi-6">
                        <button type="button" class="btn-import mx-3- p-t-3 p-b-3"><i class="fa fa-download"></i> &nbsp; Import</button> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    </div>
                </div>
            </div>

            <div class="table-responsive">

                <table class="table table-responsive table-striped f-s-12">
                    <thead>
                    <tr>
                        <th scope="col">
                            <input class="form-check-input mt-0 ckbox-1-4" type="checkbox" value="" aria-label="Checkbox for following text input">
                            Serial No
                        </th>
                        <th scope="col">Create Date</th>
                        <th scope="col">Name</th>
                        <th scope="col">Mobile</th>
                        <th scope="col">Email</th>
                        <th scope="col" style="white-space: nowrap">Role</th>
                        <th scope="col">Comments</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>

                    @if(count($datas) > 0)

                        @foreach($datas as $k => $data)
                            <tr class="t-row">
                                <td>
                                    <input class="form-check-input mt-0 ckbox-1-4" type="checkbox" value="" aria-label="Checkbox for following text input">
                                    <b>{{$datas->total() - $k}}</b>
                                </td>
                                <td>{{date('jS F, Y', strtotime($data->created_at))}}</td>
                                <td>{{$data->name}}</td>
                                <td>{{$data->mobile}}</td>
                                <td>{{$data->email}}</td>
                                <td>
                                    @if(!is_null($data->spatieRole))
                                        {{$data->spatieRole->name}}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>

                                        <a href="{{url('account/'.$prefix.'/settings/users/module-permission/'.$data->id)}}"><button type="button" class="btn-edit"><i class="fa fa-edit"></i> View Permission</button></a>

                                        @if($data->is_active == 1)
                                            <a href="{{url('account/'.$prefix.'/settings/users/status/'.$data->id)}}"><button type="button" class="btn-view"><i class="fa fa-eye"></i> Active</button></a>
                                        @else
                                            <a href="{{url('account/'.$prefix.'/settings/users/status/'.$data->id)}}"><button type="button" class="btn-view"><i class="fas fa-eye-slash"></i> In-active</button></a>
                                        @endif

                                        <a href="{{url('account/'.$prefix.'/settings/users/'.$data->id.'/edit')}}"><button type="button" class="btn-edit"><i class="fa fa-edit"></i> Edit</button></a>

                                        <button type="submit" class="btn-delete" data-bs-toggle="modal" data-bs-target="#bt5DeleteModal"
                                                data-url="{{url('account/'.$prefix.'/settings/users/'.$data->id)}}"
                                                data-altxt="User"
                                        ><i class="fa fa-trash"></i> Delete</button>
                                </td>
                            </tr>
                        @endforeach

                    @else
                        <tr>
                            <td colspan="6" align="center">Data Not Found</td>
                        </tr>
                    @endif
                    </tbody>
                </table>

                <div style="margin-bottom: 30px;float: right;margin-right: 80px;">
                    {{$datas->links()}}
                </div>

            </div>
        </section>
    </div>

    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>

@endsection
