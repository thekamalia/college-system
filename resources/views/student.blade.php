<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Student List</h1>
            <button class="btn btn-success" id="registerNewUser">Register New User</button>
        </div>
        <table class="table table-bordered table-hover">
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
            <tbody id="studentTableBody">
                <!-- Rows will be populated here -->
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = 'index.html';
                return;
            }

            const studentTableBody = document.getElementById('studentTableBody');

            function fetchStudents() {
                fetch('/api/students', {
                        headers: {
                            Authorization: `Bearer ${token}`
                        }
                    }).then(response => response.json())
                    .then(data => {
                        studentTableBody.innerHTML = '';
                        data.forEach(student => {
                            const row = `
                            <tr>
                                <td>${student.first_name}</td>
                                <td>${student.last_name}</td>
                                <td>${student.address}</td>
                                <td>${student.registration_number}</td>
                                <td>${student.contact_number}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="editStudent(${student.id})">Edit</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteStudent(${student.id})">Delete</button>
                                </td>
                            </tr>
                        `;
                            studentTableBody.innerHTML += row;
                        });
                    });
            }

            fetchStudents();

            document.getElementById('registerNewUser').addEventListener('click', () => {
                // Navigate to a form or modal for adding new users
                alert('Navigate to the registration form!');
            });

            window.editStudent = (id) => {
                alert(`Edit student with ID: ${id}`);
                // Implement edit functionality
            };

            window.deleteStudent = (id) => {
                if (confirm('Are you sure you want to delete this student?')) {
                    fetch(`/api/students/${id}`, {
                            method: 'DELETE',
                            headers: {
                                Authorization: `Bearer ${token}`
                            }
                        }).then(response => response.json())
                        .then(data => {
                            alert(data.message || 'Student deleted successfully!');
                            fetchStudents();
                        });
                }
            };
        });
    </script>
</body>

</html>
