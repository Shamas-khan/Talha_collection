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

                            <h3 class="m-0">Monthly Salary</h3>
                            <div class="clearfix"></div>
                        </div>



                        <div class="mb-3 col-2 float-left">
                            <label class="form-label">Salary Month</label>
                            <input type="month" name="attendance_month" readonly id="attendance_month"
                                class="form-control" value="{{ $attendanceDatee }}">
                            @error('attendance_month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-2 float-left ">
                            <label class="form-label">Holidays</label>
                            <input type="number" name="" readonly id="" class="form-control"
                                value="{{ $totalHolidays }}">

                        </div>

                        <form id="attendance-form" action="{{ route('payroll.store') }}" method="POST">
                            @csrf





                            <div class="x_content">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card-box table-responsive" style="overflow-y: hidden; padding: 10px 0;">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>F-Name</th>
                                                        <th>B-Salary</th>
                                                        <th>Work Days</th>
                                                        <th>Late</th>
                                                        <th>Early</th>
                                                        {{-- <th>Work Hours</th> --}}
                                                        {{-- <th>Holidays</th> --}}
                                                        <th>Absent</th>
                                                        <th>Leaves</th>
                                                        <th>Deduction</th>
                                                        <th>Allowances</th>
                                                        <th>Gross Salary</th>
                                                        <th>Net Salary</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
        <input type="hidden" name="salary_month" value="{{ $attendanceDate }}">
        <input type="hidden" name="days_holidays" value="{{ $totalHolidays }}">
                                                                
                                                    @foreach ($salaryDetails as $detail)
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" name="employe_id[]"
                                                                    value="{{ $detail['employee_id'] }}">
                                                                {{ $detail['name'] }}
                                                            </td>
                                                            <td>{{ $detail['fname'] }}</td>
                                                            <td>
            <input type="hidden" name="basic_salary[]" value="{{ $detail['basic_salary'] }}">
                                                           
                                                                {{ $detail['basic_salary'] }}</td>
                                                            <td>

                                                                <input type="hidden" name="work_days[]"
                                                                    value="{{ $detail['work_days'] }}">

                                                                {{ $detail['work_days'] }}
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="late_days[]"
                                                                    value="{{ $detail['late_days'] }}">
                                                                {{ $detail['late_days'] }}
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="early_days[]"
                                                                    value="{{ $detail['early_days'] }}">
                                                                {{ $detail['early_days'] }}
                                                            </td>
                                                            {{-- <td>{{ $detail['work_hours'] }}</td> --}}
                                                            {{-- <td>{{ $detail['holidays'] }}</td> --}}
                                                            <td>
                                                                <input type="hidden" name="absent[]"
                                                                    value="{{ $detail['absent'] }}">
                                                                {{ $detail['absent'] }}
                                                            </td>
                                                            <td><input type="hidden" name="leaves[]"
                                                                    value="{{ $detail['leaves'] }}">
                                                                {{ $detail['leaves'] }}</td>
                                                            <td>
                                                                <input type="hidden" name="deduction[]"
                                                                    value="{{ $detail['deduction'] }}">
                                                                {{ $detail['deduction'] }}
                                                            </td>

                                                            <td>
                                                                <input type="number" name="allowances[]"
                                                                    value=" {{ $detail['allowances'] }}"  style="width: 60px">
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="gross_salary[]"
                                                                    value="{{ $detail['gross_salary'] }}">

                                                                {{ $detail['gross_salary'] }}
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="net_salary[]"
                                                                    value="{{ $detail['net_salary'] }}">

                                                                {{ $detail['net_salary'] }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>

                                            <button type="submit" class="btn btn-primary">Generate</button>
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
    <?php $file = 'dummy.js'; ?>
    <!-- /page content -->
@endsection
