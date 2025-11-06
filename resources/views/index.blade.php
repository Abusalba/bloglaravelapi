<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laravel API CRUD + Auth</title>
    <style>
        body {
            font-family: Arial;
            margin: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background: #333;
            color: #fff;
        }

        form {
            margin-bottom: 20px;
        }

        .hidden {
            display: none;
        }

        button {
            margin-right: 5px;
        }
    </style>
</head>

<body>

    <h2>Laravel API CRUD + Login Example</h2>

    <!-- LOGIN FORM -->
    <div id="loginSection">
        <h3>Login</h3>
        <input type="text" id="email" placeholder="Email">
        <input type="password" id="password" placeholder="Password">
        <button onclick="loginUser()">Login</button>
        <button onclick="showRegister()">Create Account</button>
    </div>

    <!-- REGISTER FORM -->
    <div id="registerSection" class="hidden">
        <h3>Register</h3>
        <input type="text" id="name" placeholder="Name">
        <input type="text" id="reg_email" placeholder="Email">
        <input type="password" id="reg_password" placeholder="Password">
        <button onclick="registerUser()">Register</button>
        <button onclick="showLogin()">Back to Login</button>
    </div>

    <!-- POST CRUD SECTION -->
    <div id="appSection" class="hidden">
        <h3>Welcome! Manage Posts</h3>
        <button onclick="logoutUser()">Logout</button>

        <form id="postForm">
            <input type="hidden" id="editId">
            <input type="text" id="title" placeholder="Title" required>
            <input type="text" id="description" placeholder="Description" required>
            <button type="submit">Save</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="postsTable"></tbody>
        </table>
    </div>

    <script>
        const API_BASE = "http://127.0.0.1:8000/api";
        let token = localStorage.getItem('token');

        // Toggle Views
        function showLogin() {
            document.getElementById('loginSection').classList.remove('hidden');
            document.getElementById('registerSection').classList.add('hidden');
        }

        function showRegister() {
            document.getElementById('registerSection').classList.remove('hidden');
            document.getElementById('loginSection').classList.add('hidden');
        }

        // ================= LOGIN =================
        async function loginUser() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const res = await fetch(`${API_BASE}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email,
                    password
                })
            });
            const data = await res.json();
            if (data.token) {
                localStorage.setItem('token', data.token);
                token = data.token;
                loadPosts();
            } else {
                alert('Login failed');
            }
        }

        // ================= REGISTER =================
        async function registerUser() {
            const name = document.getElementById('name').value;
            const email = document.getElementById('reg_email').value;
            const password = document.getElementById('reg_password').value;
            const res = await fetch(`${API_BASE}/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name,
                    email,
                    password
                })
            });
            const data = await res.json();
            if (data.success) {
                alert('Registration successful, please login!');
                showLogin();
            } else {
                alert('Failed to register');
            }
        }

        // ================= LOGOUT =================
        function logoutUser() {
            localStorage.removeItem('token');
            token = null;
            document.getElementById('appSection').classList.add('hidden');
            document.getElementById('loginSection').classList.remove('hidden');
        }

        // ================= FETCH POSTS =================
        async function loadPosts() {
            const res = await fetch(`${API_BASE}/posts`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            const data = await res.json();
            if (data.data) {
                document.getElementById('loginSection').classList.add('hidden');
                document.getElementById('appSection').classList.remove('hidden');

                const tbody = document.getElementById('postsTable');
                tbody.innerHTML = '';
                data.data.forEach(p => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${p.id}</td>
                            <td>${p.title}</td>
                            <td>${p.description}</td>
                            <td>
                                <button onclick="editPost(${p.id}, '${p.title}', '${p.description}')">Edit</button>
                                <button onclick="deletePost(${p.id})">Delete</button>
                            </td>
                        </tr>`;
                });
            } else {
                alert('Failed to load posts');
            }
        }

        // ================= CREATE / UPDATE =================
        document.getElementById('postForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('editId').value;
            const title = document.getElementById('title').value;
            const description = document.getElementById('description').value;

            const method = id ? 'PUT' : 'POST';
            const url = id ? `${API_BASE}/posts/${id}` : `${API_BASE}/posts`;

            const res = await fetch(url, {
                method,
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    title,
                    description
                })
            });
            const data = await res.json();
            alert(data.message || 'Saved!');
            document.getElementById('postForm').reset();
            document.getElementById('editId').value = '';
            loadPosts();
        });

        // ================= EDIT =================
        function editPost(id, title, description) {
            document.getElementById('editId').value = id;
            document.getElementById('title').value = title;
            document.getElementById('description').value = description;
        }

        // ================= DELETE =================
        async function deletePost(id) {
            if (!confirm('Delete this post?')) return;
            const res = await fetch(`${API_BASE}/posts/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            const data = await res.json();
            alert(data.message || 'Deleted!');
            loadPosts();
        }

        // Auto-login if token already saved
        if (token) loadPosts();
    </script>
</body>

</html>