@extends('core::layouts.master')
@section('content')
    @include('core::inc.toaster-message')
    <section class="login-page-container">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">

                    <!--start design -->
                    <div class="login-section">
                        <div class="row">
                            <div class="col-md-6 right-section">
                                {{Form::open(['route'=>['core.login.submit',$prefix],'method'=>'POST','class'=>'login-form'])}}
                                <h5>Log in to Shujog.xyz</h5>
                                <p>Don't have a account, <a href="" style="color: #8E73BE;text-decoration: none">Sign Up</a> </p>


                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-12">
                                        <label>Username</label>
                                        <input type="text" name="email" placeholder="Username">
                                    </div>
                                </div>

                                <div class="row" style="clear: both">
                                    <div class="col-md-12">
                                        <label>Password</label>
                                        <div class="hidden-show-icon">
                                            <input type="password" name="password" placeholder="Password">
                                            <span class="passwordIcon"><i class="fas fa-eye-slash"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="clear: both">
                                    <div class="col-md-12">
                                        <label class="remember"><input type="radio" name="remember"/> Remember me</label>
                                        <a href="#" class="forget-password">Forget Password ?</a>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button>Sign In</button>
                                    </div>
                                </div>


                                {{Form::close()}}
                            </div>

                            <div class="col-md-6 left-section">
                                <h4>Shujog.xyz</h4>
                                <p>Let's Do Something New Today</p>
                                <h5>Learn.Earn.Connect</h5>
                                <img src="{{asset('Modules/ManualOrder/Public/images/login-bg.png')}}" alt="">
                            </div>
                        </div>
                    </div>
                    <!--end design -->

                </div>
            </div>

        </div>
    </section>
@endsection




