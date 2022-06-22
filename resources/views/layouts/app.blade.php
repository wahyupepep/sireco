<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Booking Room</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{asset('assets/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/css/vendor.bundle.base.css')}}">
    <link href="{{asset('assets/css/jquery.dataTables.min.css')}}" rel="stylesheet">

    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    {{-- select --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('assets/images/hetero.png') }}" />
    {{-- jquery ui --}}
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
    <style>
      .btn-warning-material {
        background:  #FFC300;
        color:white;
      }
      .btn-danger-material {
        background:  #E57373;
        color:white;
      }
      .btn-blue-material {
        background : #42a5f5;
        color:white
      }
      .btn-warning-material, .btn-danger-material, .btn-blue-material:hover {
        color:white !important
      }
     
        .red-text{
          background: #EF5350 !important;
        }
        .bg-red {
          background: #EF5350 !important;
        }
        .color-red{
          color: #EF5350 !important;
        }
        .bg-green {
          background-color: #00897B;
        }
        .bg-blue {
          background-color: #26C6DA 
        }
    </style>
    @yield('css')
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_navbar.html -->
      @include('layouts.navbar')
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        @include('layouts.sidebar')
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            @yield('content')
            @include('layouts.footer')
          </div>
        </div>
       
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{asset('assets/vendors/js/vendor.bundle.base.js')}}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{asset('assets/vendors/chart.js/Chart.min.js')}}"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{asset('assets/js/off-canvas.js')}}"></script>
    <script src="{{asset('assets/js/hoverable-collapse.js')}}"></script>
    <script src="{{asset('assets/js/misc.js')}}"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="{{asset('assets/js/dashboard.js')}}"></script>
    <script src="{{asset('assets/js/todolist.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    {{-- select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- jquery ui --}}
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
    <script>
      $(document).ready(function() {
        $('.file-upload-browse').on('click', function() {
            var file = $(this).parent().parent().parent().find('.file-upload-default');
            file.trigger('click');
          });
        $('.file-upload-default').on('change', function() {
          $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
        });
        $('#logout-form').on('click',function(e) {
          e.preventDefault()
          
          Swal.fire({
            title: 'Apakah anda yakin',
            text: "Untuk keluar dari halaman ini ",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Iya, lanjutkan!', 
            cancelButtonColor: 'Batal'
          }).then((result) => {
            if (result.isConfirmed) {
               $.ajax({
                url: "{{ route('manage.logout') }}",
                type: "post",
                data: {_token: $('meta[name="csrf-token"]').attr('content')},
                success: function(data) {
                  window.location = "{{route('manage.login')}}"
                }
              })
            }
          })
        })
      })
    </script>
    @yield('script')
    <!-- End custom js for this page -->
  </body>
</html>