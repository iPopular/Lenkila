@extends('layouts.master') @section('main')
<main class="pt-6">
    <div class="container text-xs-center">
        <section class="section">
        <h5>วิเคราะห์ข้อมูล</h5>
            <div class="row">

                <div class="col-md-4">
                    <form id="formAnalysis" class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/analysis-getStat">
                        <meta name="csrf_token" content="{{ csrf_token() }}" />
                        <div class="input-daterange input-group" id="date-analysis">
                            <input name="mount" type="text" class="input-sm form-control"/>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-2 md-form">
                        </div>
                        <div class="col-md-6 md-form">
                            <label>รายได้ทั้งหมด</label>
                        </div>
                        <div class="col-md-4 md-form">
                            <label id="income"></label>
                        </div>
                    </div>
                    </br>
                    <div class="row">
                        <div class="col-md-2 md-form">
                        </div>
                        <div class="col-md-6 md-form">
                            <label>จำนวนการจอง</label>
                        </div>
                        <div class="col-md-4 md-form">
                            <label id="count_reserve"></label>
                        </div>
                    </div>          
                </div>
                <div class="col-md-8">
                    <canvas id="lineChartEx"></canvas>
                </div>
            </div>
            <br><br><br>
            <div class="row">
                <div class="col-md-4 md-form">
                    <label style="font-size: 1.25rem;">ลูกค้าดีเด่นประจำเดือน</label>
                </div>
            </div>
            <br>
            <div class="row">                
                <div class="col-md-4 md-form">
                    <label id="best_customer"></label>
                </div>
            </div>
        </section>
    </div>
</main>
@stop