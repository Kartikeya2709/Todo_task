<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Todo App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .completed {
            text-decoration: line-through;
            color: #6c757d;
        }
        .completed-badge {
            background-color: #28a745;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-left: 8px;
        }
        .task-content {
            display: flex;
            align-items: center;
        }
        .completed-row {
            background-color: #d4edda;
            transition: background-color 0.3s ease;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #f8f9fa;
            padding: 15px 0;
            text-align: center;
            border-top: 1px solid #dee2e6;
            z-index: 1000;
        }
        .main-content {
            margin-bottom: 100px;
        }

        /* Spinner Styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            font-family: 'Arial', sans-serif;
        }

        .loading-text {
            font-weight: bold;
            font-size: 1.5rem;
            margin-top: 20px;
            color: #333;
        }

        .note-text {
            font-size: 0.875rem;
            color: #888;
            margin-top: 8px;
        }

        .footer img {
            transition: transform 0.2s ease-in-out;
        }

        .footer img:hover {
            transform: scale(1.1);
        }

        .footer small, .footer p {
            margin-bottom: 4px;
        }

        .card-header h3 {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div id="app" class="container mt-5 main-content">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Todo App</h3>
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-3">
                            <input type="text" v-model="newTask" @keyup.enter="addTask" class="form-control" placeholder="Add new task">
                            <button class="btn btn-primary" @click="addTask">Add Task</button>
                        </div>
                        
                        <div class="mb-3">
                            <button class="btn btn-secondary" @click="showAllTasks">Show All Tasks</button>
                        </div>

                        <!-- Loading Indicator -->
                        <div v-if="loading" class="loading-overlay">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="loading-text">Loading tasks...</div>
                            <div class="note-text">Note: Database may take longer to load data due to the free trial version of the service.</div>
                        </div>

                        <!-- Tasks List -->
                        <ul class="list-group">
                            <li v-for="task in tasks" :key="task.id" class="list-group-item d-flex justify-content-between align-items-center" :class="{ 'completed-row': task.completed }">
                                <div class="form-check task-content">
                                    <input class="form-check-input" type="checkbox" v-model="task.completed" @change="toggleTask(task)">
                                    <label class="form-check-label" :class="{ completed: task.completed }">
                                        @{{ task.title }}
                                    </label>
                                    <span v-if="task.completed" class="completed-badge">Completed</span>
                                </div>
                                <button class="btn btn-danger btn-sm" @click="confirmDelete(task)">Delete</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container d-flex flex-column align-items-center">
            <p class="mb-1">
                Developed by <strong>Kartikeya Sharma</strong>
                <a href="https://www.linkedin.com/in/kartikeyasharma2709/" target="_blank" class="ms-2" title="LinkedIn Profile">
                    <img src="https://cdn-icons-png.flaticon.com/512/174/174857.png" alt="LinkedIn" style="height: 20px; vertical-align: middle;">
                </a>
            </p>
            <small class="text-muted mb-1">📞 9560760585 | 📧 sharma.kartikeya2709@gmail.com</small>
            <small class="text-muted">Note: Database may take longer to load data due to the free trial version of the service.</small>
        </div>
    </footer>

    <script>
        new Vue({
            el: '#app',
            data: {
                tasks: [],
                newTask: '',
                showCompleted: true,
                loading: false, // Track loading state
            },
            mounted() {
                this.fetchTasks();
            },
            methods: {
                fetchTasks() {
                    this.loading = true;  // Show loading spinner
                    axios.get('/tasks')
                        .then(response => {
                            this.tasks = response.data;
                            this.loading = false;  // Hide loading spinner once data is fetched
                        })
                        .catch(error => {
                            console.error('Error fetching tasks:', error);
                            this.loading = false;
                        });
                },
                addTask() {
                    if (this.newTask.trim() === '') return;
                    
                    axios.post('/tasks', { title: this.newTask })
                        .then(response => {
                            this.tasks.unshift(response.data);
                            this.newTask = '';
                        })
                        .catch(error => {
                            if (error.response && error.response.status === 422) {
                                alert('This task already exists!');
                            }
                        });
                },
                toggleTask(task) {
                    axios.put(`/tasks/${task.id}`, { completed: task.completed })
                        .then(response => {
                            if (task.completed) {
                                this.tasks = this.tasks.filter(t => t.id !== task.id);
                            }
                        })
                        .catch(error => {
                            console.error('Error updating task:', error);
                            task.completed = !task.completed;
                        });
                },
                confirmDelete(task) {
                    if (confirm('Are you sure you want to delete this task?')) {
                        this.deleteTask(task);
                    }
                },
                deleteTask(task) {
                    axios.delete(`/tasks/${task.id}`)
                        .then(() => {
                            this.tasks = this.tasks.filter(t => t.id !== task.id);
                        })
                        .catch(error => {
                            console.error('Error deleting task:', error);
                        });
                },
                showAllTasks() {
                    this.fetchTasks();
                }
            }
        });
    </script>
</body>
</html>
