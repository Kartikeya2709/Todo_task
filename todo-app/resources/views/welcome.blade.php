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
            }
            .main-content {
                margin-bottom: 60px;
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

        <footer class="footer">
            <div class="container">
                <p class="mb-0">Developed by <strong>Kartikeya Sharma</strong></p>
            </div>
        </footer>

        <script>
            new Vue({
                el: '#app',
                data: {
                    tasks: [],
                    newTask: '',
                    showCompleted: true
                },
                mounted() {
                    this.fetchTasks();
                },
                methods: {
                    fetchTasks() {
                        axios.get('/tasks')
                            .then(response => {
                                this.tasks = response.data;
                            })
                            .catch(error => {
                                console.error('Error fetching tasks:', error);
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
                                if (error.response.status === 422) {
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
