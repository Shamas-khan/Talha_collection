@extends('layout.layout')

@section('content')
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
               
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h3>List Employees</h3>
                        <div class="clearfix"></div>
                    </div>
                    
                    <form id="attendance-form" action="{{ route('attendance.mark') }}" method="POST">
                        @csrf
                    
                        <div class="mb-3 col-3">
                            <label for="attendance_date" class="form-label">Date</label>
                            <input type="date" name="attendance_date" id="attendance_date" 
                                   class="form-control @error('attendance_date') is-invalid @enderror" 
                                   value="{{ old('attendance_date') }}">
                            @error('attendance_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    
                        <div class="col-3 mb-3">
                            <label for="is_holiday" class="form-check-label">
                                <input type="checkbox" name="is_holiday" id="is_holiday" class="form-check-input">
                                Mark as Holiday
                            </label>
                        </div>
                    
                        <div class="x_content">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card-box table-responsive" style="overflow-y: hidden; padding: 10px 0;">
                                        <table id="ttable" class="table table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>F-Name</th>
                                                    <th>Check-In</th>
                                                    <th>Check-Out</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($employees as $index => $employee)
                                                    <tr>
                                                        <td>{{ $employee->name }}
                                                            <input type="hidden" name="employee_id[{{ $index }}]" value="{{ $employee->employee_id }}">
                                                        </td>
                                                        <td>{{ $employee->fname }}</td>
                                                        <td>
                                                            <input type="time" name="check_in[{{ $index }}]" 
                                                            class="form-control @error('check_in.' . $index) is-invalid @enderror" 
                                                            value="{{ old('check_in.' . $index, '09:00') }}">
                                                            @error('check_in.' . $index)
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <input type="time" name="check_out[{{ $index }}]" 
                                                                   class="form-control @error('check_out.' . $index) is-invalid @enderror" 
                                                                   value="{{ old('check_out.' . $index, '21:00') }}">
                                                            @error('check_out.' . $index)
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <div class="form-check">
                                                                <label class="form-check-label">
                                                                    <input type="radio" name="status[{{ $index }}]" 
                                                                           value="present" 
                                                                           class="form-check-input @error('status.' . $index) is-invalid @enderror"
                                                                           {{ old('status.' . $index, 'present') == 'present' ? 'checked' : '' }}>
                                                                    Present
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <label class="form-check-label">
                                                                    <input type="radio" name="status[{{ $index }}]" 
                                                                           value="leave" 
                                                                           class="form-check-input @error('status.' . $index) is-invalid @enderror"
                                                                           {{ old('status.' . $index) == 'leave' ? 'checked' : '' }}>
                                                                    Leave
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <label class="form-check-label">
                                                                    <input type="radio" name="status[{{ $index }}]" 
                                                                           value="absent" 
                                                                           class="form-check-input @error('status.' . $index) is-invalid @enderror"
                                                                           {{ old('status.' . $index) == 'absent' ? 'checked' : '' }}>
                                                                    Absent
                                                                </label>
                                                            </div>
                                                            @error('status.' . $index)
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <button type="submit" class="btn btn-primary">Mark Attendance</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php $file="dummy.js"?>
<!-- /page content -->
@endsection


