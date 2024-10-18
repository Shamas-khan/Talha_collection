
<footer>
          <div class="pull-right">
          <a href="javascript:void(0)">applet services</a>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>


    <!-- Bootstrap -->
   <script src="{{asset('assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
    <!-- FastClick -->
    {{-- <script src="{{asset('assets/vendor/fastclick/lib/fastclick.js')}}"></script> --}}
    <!-- NProgress -->
    <script src="{{asset('assets/vendor/nprogress/nprogress.js')}}"></script>
    <!-- iCheck -->
    <script src="{{asset('assets/vendor/iCheck/icheck.min.js')}}"></script>
     <!-- Chart.js')}} -->
     {{-- <script src="{{asset('assets/vendor/Chart.js/dist/Chart.min.js')}}"></script> --}}
    <!-- gauge.js')}} -->
    {{-- <script src="{{asset('assets/vendor/gauge.js/dist/gauge.min.js')}}"></script> --}}
    <!-- bootstrap-progressbar -->
    {{-- <script src="{{asset('assets/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js')}}"></script> --}}
    <!-- iCheck -->
    {{-- <script src="{{asset('assets/vendor/iCheck/icheck.min.js')}}"></script> --}}
    <!-- Skycons -->
    {{-- <script src="{{asset('assets/vendor/skycons/skycons.js')}}"></script> --}}
    <!-- Flot -->
    {{-- <script src="{{asset('assets/vendor/Flot/jquery.flot.js')}}"></script>
    <script src="{{asset('assets/vendor/Flot/jquery.flot.pie.js')}}"></script>
    <script src="{{asset('assets/vendor/Flot/jquery.flot.time.js')}}"></script>
    <script src="{{asset('assets/vendor/Flot/jquery.flot.stack.js')}}"></script>
    <script src="{{asset('assets/vendor/Flot/jquery.flot.resize.js')}}"></script> --}}
    <!-- Flot plugins -->
    {{-- <script src="{{asset('assets/vendor/flot.orderbars/js/jquery.flot.orderBars.js')}}"></script>
    <script src="{{asset('assets/vendor/flot-spline/js/jquery.flot.spline.min.js')}}"></script>
    <script src="{{asset('assets/vendor/flot.curvedlines/curvedLines.js')}}"></script> --}}
    <!-- DateJS -->
    {{-- <script src="{{asset('assets/vendor/DateJS/build/date.js')}}"></script> --}}
    <!-- JQVMap -->
    {{-- <script src="{{asset('assets/vendor/jqvmap/dist/jquery.vmap.js')}}"></script> --}}
    {{-- <script src="{{asset('assets/vendor/jqvmap/dist/maps/jquery.vmap.world.js')}}"></script>
    <script src="{{asset('assets/vendor/jqvmap/examples/js/jquery.vmap.sampledata.js')}}"></script> --}}
    <!-- bootstrap-daterangepicker -->
    {{-- <script src="{{asset('assets/vendor/moment/min/moment.min.js')}}"></script>
    <script src="{{asset('assets/vendor/bootstrap-daterangepicker/daterangepicker.js')}}"></script> --}}

   
    {{-- <script src="{{asset('assets/vendor/jszip/dist/jszip.min.js')}}"></script> --}}
    {{-- <script src="{{asset('assets/vendor/pdfmake/build/pdfmake.min.js')}}"></script>
    <script src="{{asset('assets/vendor/pdfmake/build/vfs_fonts.js')}}"></script> --}}
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.3/jquery.validate.min.js"></script> --}}
  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="{{asset('assets/js/custom.js')}}"></script>

    
      <?php if($file){
       ?>
       <script src="{{asset('assets/js/'.$file)}}"></script>
       <?php
    }?>

</body>
</html>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Toastr options
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        @if(session()->has('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session()->has('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>