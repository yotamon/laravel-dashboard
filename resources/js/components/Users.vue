<template>
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Responsive Hover Table</h3>

                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <button type="submit" class="btn btn-success" @click="newUserModal"><i class="fas fa-user-plus fa-fw"></i>Add New</button>
                                <!-- <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                    <div class="input-group-append">
                      <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div> -->
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Registration Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users" :key="user.id">
                                    <td>{{user.id}}</td>
                                    <td>{{user.name}}</td>
                                    <td>{{user.email}}</td>
                                    <td>{{user.type | upText}}</td>
                                    <td>{{user.created_at | fixDate}}</td>
                                    <td>
                                        <a href="#" @click="editUserModal(user)"><i class="fa fa-edit blue"></i></a>/
                                        <a href="#" @click="deleteUser(user.id)"><i class="fa fa-trash red"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>

        <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel" v-show="!editmode">Add New User</h5>
                        <h5 class="modal-title" id="exampleModalLabel" v-show="editmode">Edit User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editmode ? updateUser() : createUser()">
                        <div class="modal-body">
                            <div class="form-group">
                                <input v-model="form.name" type="text" name="name" placeholder="Name" id="name"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <input v-model="form.email" type="text" name="email" placeholder="E-Mail" id="email"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"></has-error>
                            </div>
                            <div class="form-group">
                                <input v-model="form.bio" type="text" name="bio" placeholder="Short Bio (optional)"
                                    id="bio" class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }">
                                <has-error :form="form" field="bio"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="form.type" type="text" name="type" placeholder="User Type" id="type"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                    <option value="">Select User Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">Standard User</option>
                                    <option value="author">Author</option>
                                </select>
                                <has-error :form="form" field="type"></has-error>
                            </div>
                            <div class="form-group">
                                <input v-model="form.password" type="text" name="password" placeholder="Password"
                                    id="password" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('password') }">
                                <has-error :form="form" field="password"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" v-show="!editmode">Create</button>
                            <button type="submit" class="btn btn-success" v-show="editmode">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                editmode: false,
                users: {},
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: ''
                })
            }
        },
        methods: {
            editUserModal(user) {
                this.editmode = true;
                this.form.reset();
                this.form.fill(user);
                $('#userModal').modal('show');
            },
            newUserModal() {
                this.editmode = false;
                this.form.reset();
                $('#userModal').modal('show');
            },
            loadUsers() {
                axios.get('api/user').then(({data}) => (this.users = data.data))
            },
            createUser() {
                this.$Progress.start();
                this.form.post('api/user')
                .then(()=> {
                    Fire.$emit('userDataChanged');
                    $('#userModal').modal('hide');

                    toast.fire({icon: 'success', title: 'User Created'})

                    this.$Progress.finish();
                })
                .catch(()=> {
                    toast.fire({icon: 'fail', title: 'Something Went Wrong'})
                });
            },
            deleteUser(id) {
                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.form.delete('api/user/'+id).then((res)=> {
                                swal.fire('Deleted!', 'User was deleted succesfully', 'success')
                                Fire.$emit('userDataChanged');
                            })
                        }
                    }).catch(()=> {
                        swal.fire('Failed!', 'User deletion went wrong.', 'warning');
                    })
            },
            updateUser() {
                this.$Progress.start();
                this.form.put('api/user/' + this.form.id)
                .then((res)=> {
                    swal.fire('User Updated!', res.data.message, 'success')
                    Fire.$emit('userDataChanged');
                    $('#userModal').modal('hide');
                })
                .catch(()=> {
                    swal.fire('User Update Failed!', res.data.message, 'warning')
                    this.$Progress.fail();
                })
            }
        },
        created() {
            this.loadUsers();

            Fire.$on('userDataChanged', () => {
                this.loadUsers();
            })}
    }

</script>
