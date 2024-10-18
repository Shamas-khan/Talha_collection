<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap -->
   
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    {{-- <link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"> --}}
    <link href="{{asset('assets/vendor/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{asset('assets/vendor/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{asset('assets/vendor/nprogress/nprogress.css')}}" rel="stylesheet">
    <!-- icon -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> --}}
    <!-- iCheck -->
    <link href="{{asset('assets/vendor/iCheck/skins/flat/green.css')}}" rel="stylesheet">
    <!-- bootstrap-progressbar -->
    {{-- <link href="{{asset('assets/vendor/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css')}}" rel="stylesheet"> --}}
    <!-- JQVMap -->
    {{-- <link href="{{asset('assets/vendor/jqvmap/dist/jqvmap.min.css')}}" rel="stylesheet"/> --}}
    <!-- bootstrap-daterangepicker -->
    {{-- <link href="{{asset('assets/vendor/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet"> --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <!-- Custom Theme Style -->
    <link href="{{asset('assets/css/custom.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="{{asset('assets/vendor/jquery/dist/jquery.min.js')}}"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
  <style>
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(1.5em + .75rem + 2px);
        padding: .375rem .75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: .25rem;
        border: 1px solid #ced4da;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + .75rem + 2px);
        right: 10px;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        padding-right: 10px;
    }
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

   
    input[type=number] {
        -moz-appearance: textfield;
    }
      
      .table-container {
            overflow-x: auto;
            width: 100%; 
        }

       
        .table-container::-webkit-scrollbar {
            height: 12px; 
        }
        .mCustomScrollbar::-webkit-scrollbar {
            width: 10px;
            height: 1px; 
        }

        .table-container::-webkit-scrollbar-thumb {
            background-color: #556675; 
            border-radius: 8px; 
            
             
        }
        .mCustomScrollbar::-webkit-scrollbar-thumb {
           
            background-color: #2a3f54; 
            border-radius: 8px; 
            width: 1px;
            height: 1px; 
            
        }

        .mCustomScrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #41576d; 
        }
        .table-container::-webkit-scrollbar-thumb:hover {
            background-color: #42586e; 
        }

        .mCustomScrollbar::-webkit-scrollbar-track {
            background-color: #2a3f54; 
            border-radius: 8px; 
        }
        .table-container::-webkit-scrollbar-track {
            background-color: #ecf0f1; 
            border-radius: 8px; 
        }

        /* th, td {
            white-space: nowrap;
        }
         .wrap-text {
            white-space: normal;
            word-wrap: break-word; 
            overflow-wrap: break-word;
        } */

       
       table.dataTable {
            width: 100% !important;
        } 
        .actionpadding{
  padding: .05rem .2rem !important;
}
.dropdown-menu{
    min-width: 8rem !important;
}
.dropdown-item {
    width: 100%;
    padding: 0px 0px !important;
}
.btn-group-sm>.btn, .btn-sm{
    padding: .05rem .5rem !important;
}
.dropdown-menu > li > a{
    width: 100%;
    display: block;
    padding: 5% 8px;
}
.dropdown-item.active, .dropdown-item:active{
       background-color: transparent !important; 
}
.is-invalid{
    
    border-color: #dc3545 !important;
    padding-right: calc(1.5em + .75rem) !important;
    background-image: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org…'/%3e%3ccircle cx='3' cy='3' r='.5'/%3e%3c/svg%3E) ;
    background-repeat: no-repeat !important;
    background-position: center right calc(.375em + .1875rem) !important;
    background-size: calc(.75em + .375rem) calc(.75em + .375rem) !important;

}
.is-invalid {
   color: #dc3545;
   border-color: #dc3545 !important;
}
.is-valid {
    border-color: #28a745;
}
.select2-container--default .select2-selection--single.is-invalid {
    border: 1px solid #dc3545; /* Adjust border color for error */
}

.select2-container--default .select2-selection--single.is-invalid .select2-selection__rendered {
    color: #dc3545; /* Optional: adjust text color for error */
}
.btn-group-sm>.btn, .btn-sm{
    padding: 0.2rem .5rem !important;
}
.pointer{
    cursor: pointer;
}
.a.text-light:focus, a.text-light:hover{
    color: #ffffff !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
    border-right: none;
}

</style>
</head>
<body class="nav-md footer_fixed ">
    <div class="container body">
      <div class="main_container">

        @include('parts.header')

@yield('content')

@include('parts.footer')
  <!-- jQuery -->
 