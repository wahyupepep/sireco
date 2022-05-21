@extends('layouts.app')

@section('content')
      <div class="page-header">
        <h3 class="page-title">
          <span class="page-title-icon text-white mr-2 red-text">
            <i class="mdi mdi-home"></i>
          </span> Dashboard
        </h3>
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">
              <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm align-middle" style="color: #EF5350"></i>
            </li>
          </ul>
        </nav>
      </div>
      <div class="row">
        <div class="col-md-4 stretch-card grid-margin">
          <div class="card bg-red card-img-holder text-white">
            <div class="card-body">
              <img src="{{asset('assets/images/dashboard/circle.svg')}}" class="card-img-absolute" alt="circle-image">
              <h4 class="font-weight-normal mb-3">Kategori Produk <i class="mdi mdi-food-fork-drink mdi-24px float-right"></i>
              </h4>
              <h2 class="mb-5">{{$categories}}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-4 stretch-card grid-margin">
          <div class="card bg-red card-img-holder text-white">
            <div class="card-body">
              <img src="{{asset('assets/images/dashboard/circle.svg')}}" class="card-img-absolute" alt="circle-image">
              <h4 class="font-weight-normal mb-3">Menu <i class="mdi mdi-food-variant mdi-24px float-right"></i>
              </h4>
              <h2 class="mb-5">{{$menus}}</h2>
            </div>
          </div>
        </div>
        @can('user-list')
        <div class="col-md-4 stretch-card grid-margin">
          <div class="card bg-red card-img-holder text-white">
            <div class="card-body">
              <img src="{{asset('assets/images/dashboard/circle.svg')}}" class="card-img-absolute" alt="circle-image">
              <h4 class="font-weight-normal mb-3">User <i class="mdi mdi-account-check mdi-24px float-right"></i>
              </h4>
              <h2 class="mb-5">{{$users}}</h2>
            </div>
          </div>
        </div>
        @endcan
       
      </div>
@endsection
