const API_BASE = 'http://localhost:8000/api';

let tours = [], clients = [], bookings = [];

document.addEventListener('DOMContentLoaded', async () => {
    console.log('Загрузка данных...');
    await loadTours('available');
    await loadClients();
    await loadBookings();
    populateSelects();
});

async function apiRequest(endpoint, options = {}) {
    try {
        console.log(`Запрос: ${API_BASE}${endpoint}`);
        const response = await fetch(`${API_BASE}${endpoint}`, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        const data = await response.json();
        console.log(`Ответ:`, data);
        return data;
    } catch (error) {
        console.error('API Error:', error);
        return { error: true, message: error.message };
    }
}

async function loadTours(type = 'available') {
    const endpoint = type === 'available' ? '/tours?available=true' : '/tours';
    const data = await apiRequest(endpoint);
    
    if (data && !data.error && data.data) {
        tours = data.data;
        renderTours(tours);
    } else {
        console.error('Нет данных туров:', data);
    }
}

function renderTours(tours) {
    const container = document.getElementById('tours-list');
    if (!container) return console.error('Нет #tours-list');
    
    container.innerHTML = tours.map(tour => `
        <div class="card">
            <h3>${tour.name || 'Без названия'}</h3>
            <p><strong>${tour.country_name || 'Страна неизвестна'}</strong></p>
            <p>${tour.start_date || ''} - ${tour.end_date || ''}</p>
            <p>${tour.description || ''}</p>
            <div class="price">${tour.price || 0}₽</div>
            <div>Мест: ${tour.available_spots || 0}/${tour.max_people || 0}</div>
        </div>
    `).join('') || '<p>Туры не найдены</p>';
}

async function loadClients() {
    const data = await apiRequest('/clients');
    if (data && !data.error && data.data) {
        clients = data.data;
        renderClients(clients);
    }
}

function renderClients(clients) {
    const container = document.getElementById('clients-list');
    container.innerHTML = clients.map(client => `
        <div class="card">
            <h3>${client.full_name}</h3>
            <p>Тел: ${client.phone}</p>
            <p>Email: ${client.email}</p>
        </div>
    `).join('') || '<p>Клиенты не найдены</p>';
}

async function loadBookings() {
    const data = await apiRequest('/bookings');
    if (data && !data.error && data.data) {
        bookings = data.data;
        renderBookings(bookings);
    }
}

function renderBookings(bookings) {
    const container = document.getElementById('bookings-list');
    container.innerHTML = bookings.map(booking => `
        <div class="card">
            <h3>${booking.client_name || 'Клиент'}</h3>
            <p><strong>${booking.tour_name || 'Тур'}</strong></p>
            <p>Дата: ${booking.booking_date}</p>
            <div class="price">${booking.total_price}₽</div>
        </div>
    `).join('') || '<p>Бронирования не найдены</p>';
}

function showSection(sectionId) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.getElementById(sectionId).classList.add('active');
}

function showBookingForm() {
    document.getElementById('booking-form').style.display = 'flex';
    populateSelects();
}

function hideBookingForm() {
    document.getElementById('booking-form').style.display = 'none';
}

function populateSelects() {
    const clientSelect = document.getElementById('clientSelect');
    const tourSelect = document.getElementById('tourSelect');
    
    clientSelect.innerHTML = clients.map(client => 
        `<option value="${client.id}">${client.full_name}</option>`
    ).join('');
    
    tourSelect.innerHTML = tours.map(tour => 
        `<option value="${tour.id}">${tour.name} (${tour.available_spots}/${tour.max_people})</option>`
    ).join('');
}

document.getElementById('bookingForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        client_id: document.getElementById('clientSelect').value,
        tour_id: document.getElementById('tourSelect').value,
        booking_date: document.getElementById('bookingDate').value,
        status: 'confirmed',
        total_price: document.getElementById('totalPrice').value
    };
    
    const response = await apiRequest('/bookings', {
        method: 'POST',
        body: JSON.stringify(formData)
    });
    
    if (response.success) {
        hideBookingForm();
        loadBookings();
        alert('Бронирование создано!');
    } else {
        alert('Ошибка: ' + (response.message || 'Неизвестная ошибка'));
    }
});
