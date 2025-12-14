@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if(isset($nonRepeatingCount) && $nonRepeatingCount > 0)
                <div class="alert alert-warning" style="margin-top: 20px; padding: 15px; border-left: 4px solid #f0ad4e;">
                    <h4 style="margin-top: 0;">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> 
                        Non-Repeating Customers Alert
                    </h4>
                    <p style="margin-bottom: 10px; font-size: 16px;">
                    </p>
                    <p style="margin-bottom: 0;">
                        <a href="{{ route('clients.nonrepeating') }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-list" aria-hidden="true"></i> View Non-Repeating Customers
                        </a>
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Popular Services Section -->
    <div class="row" style="margin-top: 30px;">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-bar-chart" aria-hidden="true"></i> Most Popular Services
                    </h3>
                </div>
                <div class="panel-body">
                    <!-- Filter Buttons -->
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-md-12">
                            <div class="btn-group" role="group" aria-label="Filter">
                                <button type="button" class="btn btn-default filter-btn active" data-filter="weekly">
                                    Weekly
                                </button>
                                <button type="button" class="btn btn-default filter-btn" data-filter="monthly">
                                    Monthly
                                </button>
                                <button type="button" class="btn btn-default filter-btn" data-filter="yearly">
                                    Yearly
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Indicator -->
                    <div id="loading-indicator" style="display: none; text-align: center; padding: 20px;">
                        <i class="fa fa-spinner fa-spin fa-3x"></i>
                        <p>Loading data...</p>
                    </div>

                    <!-- Table -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="popularServicesTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Service Name</th>
                                            <th>Total Quantity</th>
                                            <th>Total Sales</th>
                                            <th>Total Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody id="popularServicesTableBody">
                                        <tr>
                                            <td colspan="5" style="text-align: center;">Loading data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    var currentFilter = 'monthly';

    $(document).ready(function() {
        // Load initial data
        loadPopularServices('monthly');

        // Handle filter button clicks
        $('.filter-btn').on('click', function() {
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            var filter = $(this).data('filter');
            currentFilter = filter;
            loadPopularServices(filter);
        });
    });

    function loadPopularServices(filter) {
        $('#loading-indicator').show();
        $('#popularServicesTableBody').html('<tr><td colspan="5" style="text-align: center;">Loading data...</td></tr>');

        $.ajax({
            url: '{{ route("home.popularservices") }}',
            method: 'GET',
            data: {
                filter: filter,
                _token: _token
            },
            success: function(response) {
                $('#loading-indicator').hide();
                
                if (response.success && response.data.length > 0) {
                    renderTable(response.data);
                } else {
                    $('#popularServicesTableBody').html('<tr><td colspan="5" style="text-align: center;">No data available for the selected period.</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                $('#loading-indicator').hide();
                $('#popularServicesTableBody').html('<tr><td colspan="5" style="text-align: center; color: red;">Error loading data. Please try again.</td></tr>');
                console.error('Error:', error);
            }
        });
    }

    function renderTable(data) {
        var tableBody = $('#popularServicesTableBody');
        tableBody.empty();

        if (data.length === 0) {
            tableBody.html('<tr><td colspan="5" style="text-align: center;">No data available.</td></tr>');
            return;
        }

        data.forEach(function(item, index) {
            var row = '<tr>' +
                '<td>' + (index + 1) + '</td>' +
                '<td>' + (item.service_name || 'N/A') + '</td>' +
                '<td>' + parseInt(item.total_quantity || 0).toLocaleString() + '</td>' +
                '<td>' + parseInt(item.total_sales || 0).toLocaleString() + '</td>' +
                '<td>' + parseFloat(item.total_revenue || 0).toFixed(2).toLocaleString() + '</td>' +
                '</tr>';
            tableBody.append(row);
        });
    }
</script>
@endsection
