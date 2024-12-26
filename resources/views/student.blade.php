<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 30px;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .table-dark {
            background-color: #343a40;
        }

        .modal-header {
            background-color: #007bff;
            color: white;
        }

        .modal-title {
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            color: #6c757d;
            font-size: 1.2em;
            padding: 20px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Student List</h1>
            <button class="btn btn-success" id="registerNewUser">Register New Student</button>
        </div>
        <div id="noDataMessage" class="no-data" style="display: none;">
            No data available.
        </div>
        <table class="table table-bordered table-hover" id="studentTable" style="display: none;">
            <thead class="table-dark">
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Address</th>
                    <th>Registration Number</th>
                    <th>Contact Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentTableBody"></tbody>
        </table>
    </div>

    <div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentModalLabel">Register Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="studentForm">
                        <input type="hidden" id="studentId">
                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" required>
                        </div>
                        <div class="mb-3">
                            <label for="contactNumber" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contactNumber" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const token = localStorage.getItem('token');
            const studentTableBody = document.getElementById('studentTableBody');
            const studentTable = document.getElementById('studentTable');
            const noDataMessage = document.getElementById('noDataMessage');
            const studentModal = new bootstrap.Modal(document.getElementById('studentModal'));
            const studentForm = document.getElementById('studentForm');
            const studentIdField = document.getElementById('studentId');

            function fetchStudents() {
                fetch('/api/students', {
                        headers: {
                            Authorization: `Bearer ${token}`
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        studentTableBody.innerHTML = '';
                        if (data.length === 0) {
                            noDataMessage.style.display = 'block';
                            studentTable.style.display = 'none';
                        } else {
                            noDataMessage.style.display = 'none';
                            studentTable.style.display = 'table';
                            data.forEach(student => {
                                const row = `
                                <tr>
                                    <td>${student.first_name}</td>
                                    <td>${student.last_name}</td>
                                    <td>${student.address}</td>
                                    <td>${student.registration_number}</td>
                                    <td>${student.contact_number}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="editStudent(${student.id})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteStudent(${student.id})">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            `;
                                studentTableBody.innerHTML += row;
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error fetching data.');
                    });
            }

            function saveStudent(data, id) {
                const url = id ? `/api/students/${id}` : '/api/students';
                const method = id ? 'PUT' : 'POST';

                fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            Authorization: `Bearer ${token}`
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(() => {
                        studentModal.hide();
                        fetchStudents();
                    });
            }

            function deleteStudent(id) {
                if (confirm('Are you sure you want to delete this student?')) {
                    fetch(`/api/students/${id}`, {
                            method: 'DELETE',
                            headers: {
                                Authorization: `Bearer ${token}`
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                fetchStudents();
                            } else {
                                alert('Failed to delete student');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            }

            window.editStudent = (id) => {
                fetch(`/api/students/${id}`, {
                        headers: {
                            Authorization: `Bearer ${token}`
                        }
                    })
                    .then(response => response.json())
                    .then(student => {
                        studentIdField.value = student.id;
                        document.getElementById('firstName').value = student.first_name;
                        document.getElementById('lastName').value = student.last_name;
                        document.getElementById('address').value = student.address;
                        document.getElementById('contactNumber').value = student.contact_number;
                        studentModal.show();
                    });
            };

            document.getElementById('registerNewUser').addEventListener('click', () => {
                studentForm.reset();
                studentIdField.value = '';
                studentModal.show();
            });

            studentForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const studentData = {
                    first_name: document.getElementById('firstName').value,
                    last_name: document.getElementById('lastName').value,
                    address: document.getElementById('address').value,
                    contact_number: document.getElementById('contactNumber').value,
                };
                saveStudent(studentData, studentIdField.value);
            });

            fetchStudents();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
