let currentPitchId = null;

async function loadPitches() {
    const container = document.getElementById('pitches-container');
    if (!container) return;
    
    try {
        container.innerHTML = '<div class="spinner"></div>';
        const data = await apiCall('/pitches/list.php');
        
        if (data.pitches && data.pitches.length) {
            container.innerHTML = `
                <div class="grid">
                    ${data.pitches.map(pitch => `
                        <div class="card">
                            <h3>⚽ ${escapeHtml(pitch.name)}</h3>
                            <p><strong>🏷️ Loại:</strong> ${escapeHtml(pitch.type || 'Sân bóng đá')}</p>
                            <p><strong>📍 Địa chỉ:</strong> ${escapeHtml(pitch.location || 'Đang cập nhật')}</p>
                            <p><strong>💰 Giá:</strong> ${formatCurrency(pitch.price_per_hour)}/giờ</p>
                            <button class="btn btn-primary" onclick="goToBooking(${pitch.id})">📅 Đặt sân</button>
                        </div>
                    `).join('')}
                </div>
            `;
        } else {
            container.innerHTML = '<p>Không có sân nào.</p>';
        }
    } catch (error) {
        container.innerHTML = '<p class="alert alert-error">Lỗi tải dữ liệu!</p>';
    }
}

function goToBooking(pitchId) {
    window.location.href = `/web_dat_lich_san_bong/public/booking.html?pitch_id=${pitchId}`;
}

async function loadPitchDetail() {
    const urlParams = new URLSearchParams(window.location.search);
    currentPitchId = urlParams.get('pitch_id');
    
    if (!currentPitchId) {
        window.location.href = '/web_dat_lich_san_bong/public/pitches.html';
        return;
    }
    
    try {
        const data = await apiCall(`/pitches/detail.php?id=${currentPitchId}`);
        
        if (data.pitch) {
            const container = document.getElementById('pitch-detail');
            if (container) {
                container.innerHTML = `
                    <div class="card">
                        <h2>⚽ ${escapeHtml(data.pitch.name)}</h2>
                        <p><strong>💰 Giá:</strong> ${formatCurrency(data.pitch.price_per_hour)}/giờ</p>
                        <p><strong>📝 Mô tả:</strong> ${escapeHtml(data.pitch.description || 'Sân chất lượng cao')}</p>
                    </div>
                `;
            }
        }
    } catch (error) {
        showNotification('Không tìm thấy sân!', 'error');
        setTimeout(() => {
            window.location.href = '/web_dat_lich_san_bong/public/pitches.html';
        }, 1500);
    }
}

async function handleBooking(event) {
    event.preventDefault();
    
    const date = document.getElementById('booking_date').value;
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    
    if (startTime >= endTime) {
        showNotification('Giờ kết thúc phải lớn hơn giờ bắt đầu!', 'error');
        return;
    }
    
    const submitBtn = document.querySelector('#booking-form button');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Đang xử lý...';
    
    try {
        const data = await apiCall('/bookings/create.php', {
            method: 'POST',
            body: {
                pitch_id: currentPitchId,
                booking_date: date,
                start_time: startTime,
                end_time: endTime
            }
        });
        
        if (data.success) {
            showNotification('Đặt sân thành công!', 'success');
            setTimeout(() => {
                window.location.href = '/web_dat_lich_san_bong/public/my-bookings.html';
            }, 1500);
        }
    } catch (error) {
        console.error('Booking error:', error);
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Xác nhận đặt sân';
    }
}

async function loadMyBookings() {
    const container = document.getElementById('bookings-container');
    if (!container) return;
    
    try {
        container.innerHTML = '<div class="spinner"></div>';
        const data = await apiCall('/bookings/list.php');
        
        if (data.bookings && data.bookings.length) {
            const today = new Date().toISOString().split('T')[0];
            const upcoming = data.bookings.filter(b => b.booking_date >= today && b.status !== 'cancelled');
            const past = data.bookings.filter(b => b.booking_date < today || b.status === 'cancelled');
            
            let html = '';
            
            if (upcoming.length > 0) {
                html += '<h2>⏰ Sắp diễn ra</h2>';
                html += upcoming.map(booking => `
                    <div class="card">
                        <h3>⚽ ${escapeHtml(booking.pitch_name)}</h3>
                        <p><strong>📆 Ngày:</strong> ${formatDate(booking.booking_date)}</p>
                        <p><strong>⏱️ Giờ:</strong> ${booking.start_time} - ${booking.end_time}</p>
                        <p><strong>📌 Trạng thái:</strong> 
                            ${booking.status === 'pending' ? '⏳ Chờ xác nhận' : 
                              booking.status === 'confirmed' ? '✅ Đã xác nhận' : '❌ Đã hủy'}
                        </p>
                        <button class="btn btn-danger" onclick="cancelBooking(${booking.id})">❌ Hủy lịch</button>
                    </div>
                `).join('');
            }
            
            if (past.length > 0) {
                html += '<h2>📜 Lịch sử</h2>';
                html += past.map(booking => `
                    <div class="card" style="opacity: 0.7;">
                        <h3>⚽ ${escapeHtml(booking.pitch_name)}</h3>
                        <p><strong>📆 Ngày:</strong> ${formatDate(booking.booking_date)}</p>
                        <p><strong>⏱️ Giờ:</strong> ${booking.start_time} - ${booking.end_time}</p>
                    </div>
                `).join('');
            }
            
            container.innerHTML = html;
        } else {
            container.innerHTML = '<p>Bạn chưa có lịch đặt nào.</p>';
        }
    } catch (error) {
        container.innerHTML = '<p class="alert alert-error">Lỗi tải dữ liệu!</p>';
    }
}

async function cancelBooking(bookingId) {
    if (!confirm('Bạn có chắc muốn hủy lịch đặt sân này?')) return;
    
    try {
        const data = await apiCall(`/bookings/cancel.php?id=${bookingId}`, {
            method: 'POST'
        });
        
        if (data.success) {
            showNotification('Hủy lịch thành công!', 'success');
            loadMyBookings();
        }
    } catch (error) {
        console.error('Cancel error:', error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const pathname = window.location.pathname;
    
    if (pathname.includes('pitches.html')) {
        loadPitches();
    }
    
    if (pathname.includes('booking.html')) {
        loadPitchDetail();
        const form = document.getElementById('booking-form');
        if (form) form.addEventListener('submit', handleBooking);
        
        const dateInput = document.getElementById('booking_date');
        if (dateInput) {
            dateInput.min = new Date().toISOString().split('T')[0];
        }
    }
    
    if (pathname.includes('my-bookings.html')) {
        loadMyBookings();
    }
});