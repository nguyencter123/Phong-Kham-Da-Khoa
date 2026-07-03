@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <h3 class="text-primary fw-bold"><i class="fas fa-chart-pie"></i> Báo Cáo Thống Kê</h3>
        
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="fas fa-home"></i> Về Dashboard
            </a>
            <!-- Bộ lọc thời gian -->
            <form id="filterForm" class="d-flex align-items-center gap-2 m-0">
            <select name="date_range" id="dateRange" class="form-select form-select-sm shadow-sm">
                <option value="this_month" selected>Tháng hiện tại</option>
                <option value="last_month">Tháng trước</option>
                <option value="this_week">Tuần này</option>
                <option value="custom">Tùy chỉnh...</option>
            </select>
            
            <div id="customDateGroup" class="d-none d-flex align-items-center gap-2">
                <input type="date" name="start_date" id="startDate" class="form-control form-control-sm shadow-sm">
                <span>-</span>
                <input type="date" name="end_date" id="endDate" class="form-control form-control-sm shadow-sm">
            </div>

            <!-- Export -->
            <button type="button" id="btnExport" class="btn btn-sm btn-success shadow-sm text-nowrap">
                <i class="fas fa-file-excel"></i> Xuất CSV
            </button>
            </form>
        </div>
    </div>

    <!-- Vùng hiển thị lỗi (E2) -->
    <div id="errorAlert" class="alert alert-danger d-none shadow-sm">
        <i class="fas fa-exclamation-triangle"></i> <span id="errorText"></span>
    </div>

    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 text-uppercase fw-bold mb-1">Tổng Doanh Thu</h6>
                            <h3 class="mb-0 fw-bold" id="kpiRevenue">0 đ</h3>
                        </div>
                        <div class="display-4 text-white-50 opacity-50">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 text-uppercase fw-bold mb-1">Lượt khám hoàn thành</h6>
                            <h3 class="mb-0 fw-bold" id="kpiCompleted">0</h3>
                        </div>
                        <div class="display-4 text-white-50 opacity-50">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 text-uppercase fw-bold mb-1">Số lịch bị hủy</h6>
                            <h3 class="mb-0 fw-bold" id="kpiCancelled">0</h3>
                        </div>
                        <div class="display-4 text-white-50 opacity-50">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vùng biểu đồ -->
    <div id="chartsArea" class="row">
        <!-- Biểu đồ Doanh thu (Line Chart) -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold text-primary border-0 pb-0">
                    <i class="fas fa-chart-line"></i> Biểu Đồ Doanh Thu
                </div>
                <div class="card-body">
                    <div id="noDataLine" class="text-center text-muted py-5 d-none">
                        <i class="fas fa-box-open fs-1 opacity-50 mb-3"></i>
                        <p>Chưa có dữ liệu doanh thu cho khoảng thời gian này.</p>
                    </div>
                    <canvas id="revenueLineChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Biểu đồ Tròn & Top Bác sĩ -->
        <div class="col-md-4 mb-4">
            <!-- Biểu đồ tròn -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold text-success border-0 pb-0">
                    <i class="fas fa-chart-pie"></i> Tỷ lệ theo Chuyên khoa
                </div>
                <div class="card-body">
                    <div id="noDataPie" class="text-center text-muted py-4 d-none">
                        <p>Chưa có dữ liệu.</p>
                    </div>
                    <canvas id="specialtyPieChart" style="max-height: 200px;"></canvas>
                </div>
            </div>

            <!-- Top Bác sĩ -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold text-info border-0 pb-0">
                    <i class="fas fa-user-md"></i> Top 5 Bác Sĩ (Ca khám)
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush" id="topDoctorsList">
                        <!-- Render qua JS -->
                    </ul>
                    <div id="noDataDoctors" class="text-center text-muted py-3 d-none">
                        <p class="mb-0 small">Chưa có ca khám nào hoàn thành.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tải Chart.js qua CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let lineChart = null;
    let pieChart = null;

    const dateRange = document.getElementById('dateRange');
    const customDateGroup = document.getElementById('customDateGroup');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const btnExport = document.getElementById('btnExport');
    const errorAlert = document.getElementById('errorAlert');
    const errorText = document.getElementById('errorText');
    const chartsArea = document.getElementById('chartsArea');

    // Khởi tạo các Context của Canvas
    const ctxLine = document.getElementById('revenueLineChart').getContext('2d');
    const ctxPie = document.getElementById('specialtyPieChart').getContext('2d');

    // Hàm format tiền tệ
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
    };

    // Hàm tải dữ liệu
    const loadData = () => {
        let url = `{{ route('admin.reports.data') }}?date_range=${dateRange.value}`;
        
        if (dateRange.value === 'custom') {
            if (!startDate.value || !endDate.value) return; // Đợi chọn đủ 2 ngày
            url += `&start_date=${startDate.value}&end_date=${endDate.value}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    // E2: Báo lỗi dữ liệu lớn
                    errorText.textContent = data.error;
                    errorAlert.classList.remove('d-none');
                    // Reset KPIs
                    document.getElementById('kpiRevenue').textContent = '0 đ';
                    document.getElementById('kpiCompleted').textContent = '0';
                    document.getElementById('kpiCancelled').textContent = '0';
                    
                    if (lineChart) lineChart.destroy();
                    if (pieChart) pieChart.destroy();
                    document.getElementById('topDoctorsList').innerHTML = '';
                    return;
                }

                errorAlert.classList.add('d-none');

                // 1. Cập nhật KPIs
                document.getElementById('kpiRevenue').textContent = formatCurrency(data.kpis.total_revenue);
                document.getElementById('kpiCompleted').textContent = data.kpis.completed;
                document.getElementById('kpiCancelled').textContent = data.kpis.cancelled;

                // 2. Vẽ Biểu đồ Đường (Doanh thu)
                if (lineChart) lineChart.destroy();
                
                // Xử lý E1: Nếu tổng doanh thu = 0 (tức là không có điểm mốc nào có tiền)
                let totalDataAmount = data.charts.line.data.reduce((a, b) => a + Number(b), 0);
                
                if (totalDataAmount == 0) {
                    document.getElementById('revenueLineChart').classList.add('d-none');
                    document.getElementById('noDataLine').classList.remove('d-none');
                } else {
                    document.getElementById('revenueLineChart').classList.remove('d-none');
                    document.getElementById('noDataLine').classList.add('d-none');
                    
                    lineChart = new Chart(ctxLine, {
                        type: 'line',
                        data: {
                            labels: data.charts.line.labels,
                            datasets: [{
                                label: 'Doanh thu (VNĐ)',
                                data: data.charts.line.data,
                                borderColor: '#0d6efd',
                                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            if (value >= 1000000) return value / 1000000 + 'Tr';
                                            if (value >= 1000) return value / 1000 + 'K';
                                            return value;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // 3. Vẽ Biểu đồ Tròn
                if (pieChart) pieChart.destroy();
                if (data.charts.pie.labels.length === 0) {
                    document.getElementById('specialtyPieChart').classList.add('d-none');
                    document.getElementById('noDataPie').classList.remove('d-none');
                } else {
                    document.getElementById('specialtyPieChart').classList.remove('d-none');
                    document.getElementById('noDataPie').classList.add('d-none');

                    pieChart = new Chart(ctxPie, {
                        type: 'doughnut',
                        data: {
                            labels: data.charts.pie.labels,
                            datasets: [{
                                data: data.charts.pie.data,
                                backgroundColor: ['#198754', '#0dcaf0', '#ffc107', '#fd7e14', '#6f42c1', '#d63384'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                }

                // 4. Render Top Doctors
                const ul = document.getElementById('topDoctorsList');
                ul.innerHTML = '';
                if (data.top_doctors.length === 0) {
                    document.getElementById('noDataDoctors').classList.remove('d-none');
                } else {
                    document.getElementById('noDataDoctors').classList.add('d-none');
                    data.top_doctors.forEach(doc => {
                        let li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center border-0 px-3';
                        li.innerHTML = `
                            <div>
                                <div class="fw-bold text-dark">${doc.doctor_name}</div>
                                <div class="small text-muted">${doc.specialty_name}</div>
                            </div>
                            <span class="badge bg-primary rounded-pill">${doc.total} ca</span>
                        `;
                        ul.appendChild(li);
                    });
                }
            })
            .catch(err => console.error("Error loading report data:", err));
    };

    // Lắng nghe thay đổi bộ lọc
    dateRange.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateGroup.classList.remove('d-none');
        } else {
            customDateGroup.classList.add('d-none');
            loadData();
        }
    });

    startDate.addEventListener('change', loadData);
    endDate.addEventListener('change', loadData);

    // Xuất CSV
    btnExport.addEventListener('click', function() {
        let url = `{{ route('admin.reports.export') }}?date_range=${dateRange.value}`;
        if (dateRange.value === 'custom') {
            if (!startDate.value || !endDate.value) {
                alert("Vui lòng chọn ngày để xuất.");
                return;
            }
            url += `&start_date=${startDate.value}&end_date=${endDate.value}`;
        }
        window.location.href = url;
    });

    // Tải dữ liệu lần đầu
    loadData();
});
</script>
@endsection
