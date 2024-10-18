@extends('layout.layout')
@section('content')
      <!-- page content -->
      <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h2><a href="{{route('payment.index')}}" class="btn text-white bg-primary p-2 m-2" >
                  <i class="fas fa-list"></i> List
                </a>
                </h2>
              </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
              <!-- form input mask -->
              <div class="col-md-12 col-sm-12  ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Add Payment</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form id="demo-form2" action="{{ route('payment.store') }}" method="post" class="w-100 flex-wrap d-flex form-horizontal form-label-left" novalidate="">
                      @csrf

                      <div class="mb-3 col-4">
                        <label  class="form-label">Date</label>
                        <input type="date" name="date" class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }}" value="{{ old('date') }}">
                        @error('date') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                    </div>

                    
                  
                      <div class="mb-3 col-4">
                        <label for="account_type" class="form-label">Account Type</label>
                        <select id="account_type" class="form-control custom_s" name="person_type">
                            <option selected disabled>Select Type</option>
                            @foreach($tables as $key => $table)
                            <option value="{{ $key }}" {{ old('person_type') == $key ? 'selected' : '' }}  >{{ ucfirst($key) }}</option>
                            @endforeach
                        </select>
                        @error('person_type') 
                            <span class="text-red-500 text-danger">{{ $message }}</span> 
                        @enderror
                    </div>
                  
                      <div class="mb-3 col-4">
                          <label for="person_id" class="form-label">Select Party</label>
                          <select id="person_id" class="form-control custom_s" name="person_id">
                              <option selected disabled>Select Party</option>
                              <!-- Options will be populated by AJAX -->
                          </select>
                          <span id="load" class="text-success"></span>
                          @error('person_id') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                      </div>

                     

                    <div class="mb-3 col-4 bank-field" >
                      <label for="bank_id" class="form-label">Bank</label>
                      <select class="form-control select22" name="bank_id">
                          <option value="default" selected disabled>Select Bank</option>
                          @if($bank)
                              @foreach ($bank as $d)
                              <option value="{{ $d->bank_id }}" {{ old('bank_id') == $d->bank_id ? 'selected' : '' }}>{{ $d->bank_name }} <small>{{ number_format($d->running_balance, 2) }}  </small></option>
             
                              @endforeach
                          @endif
                      </select>
                      @error('bank_id') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                  </div>
                  
                  
                      <div class="mb-3 col-4">
                        <label for="phone" class="form-label">Amount</label>
                        <input type="text" name="amount" class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" value="{{ old('amount') }}">
                        @error('amount') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                    </div>

                   
                     
                  
                      <div class="mb-3 col-4">
                          <label for="cnames" class="form-label">Narration</label>
                          <input type="text" name="narration" class="form-control {{ $errors->has('narration') ? 'is-invalid' : '' }}" value="{{ old('narration') }}">
                          @error('narration') <span class="text-red-500 text-danger">{{ $message }}</span> @enderror
                      </div>
                  
                      
                  
                      <div class="mb-3 col-12">
                      </div>
                  
                      <div class="mb-3 col-12 col-md-10 text-left">
                          <button type="submit" class="btn btn-primary btn-custom">Submit</button>
                      </div>
                  </form>
                  
                  </div>
                </div>
              </div>
              <!-- /form input mask -->
 <script>
                
                $(document).ready(function() {
  
                        function loadAccountTypeData() {
                            var type = $('#account_type').val();
                            if (type) {
                                $('#load').text('Loading....');
                                var url = '{{ route("get.payables", ":type") }}';
                                url = url.replace(':type', type); // Replace the placeholder with the actual type value

                                $.ajax({
                                    url: url,
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function(data) {
                                        var $personSelect = $('#person_id');
                                        $('#load').text('');
                                        $personSelect.empty();
                                        $personSelect.append(`<option selected disabled>Select ${type}</option>`);
                                        

                                        $.each(data, function(index, item) {
                                          if(item.running){
                                            var formattedRunning = parseFloat(item.running).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                                            $personSelect.append('<option value="' + item.id + '">' + item.name + ' <small> Rs:' + formattedRunning + '</small> ' + '</option>');
                                          }
                                          else{
                                            $personSelect.append('<option value="' + item.id + '">' + item.name + '</option>');
                                         
                                          }
                                        });

                                    },
                                    error: function(xhr) {
                                        console.log('Error:', xhr.responseText);
                                        $('#load').text('');
                                    }
                                });
                            } else {
                                $('#person_id').empty().append('<option selected disabled>Select Party</option>');
                                $('#load').text('');
                            }
                        }

                     
                        loadAccountTypeData();
                        $('#account_type').change(function() {
                            loadAccountTypeData();
                        });

                        
                        
                        
                       
                    });

                
 </script>
             
            </div>
          </div>
        </div>
        <!-- /page content -->
        <?php $file="dummy.js"?>
        @endsection