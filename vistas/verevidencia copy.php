<?php
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION['usu_id'])) {
    header("Location: ../index.php");
    exit();
}
// Validar que el usuario sea administrador
if ($_SESSION['rol_id'] != "3") {
    header("Location: dashboard.php");
    exit();
}
?>


<!-- Toasts -->
<div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer">
    <!-- Toast de éxito -->
    <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mensajefloat"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    <!-- Toast de advertencia -->
    <div id="liveToastwarning" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mensajefloatwarning"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<style>
    .toast-container {
        z-index: 9999 !important;
    }

    .ql-toolbar {
        width: 100%;
    }

    .ql-container {
        width: 100%;
    }
</style>

<?php
include('head.php')

?>

<div class="pc-container">
    <div class="pc-content"><!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="menu.php">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Seguimiento de Evidencia</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-0">Seguimiento de Evidencia</h4>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock icon-svg-primary wid-20">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg><span class="p-l-5">Private Ticket #1831786</span></h5>
                    </div>
                    <div class="card-body border-bottom py-2">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="d-inline-block mb-0">Theme customization issue</h4>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="btn-star"><a href="#!" class="btn btn-light-success btn-sm">Mark as unread</a> <a href="#!"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star icon-svg-warning wid-20">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg></a></div>
                            </div>
                        </div>
                    </div>
                    <div class="border-bottom card-body py-2">
                        <div class="row align-items-center">
                            <div class="col-md-12"><button type="button" class="btn btn-sm my-2 btn-light-success"><i class="mx-2 feather icon-message-square"></i>Post a reply</button> <button type="button" class="btn btn-sm my-2 btn-light-warning"><i class="mx-2 feather icon-edit"></i>Post a Note</button> <button type="button" class="btn btn-sm my-2 btn-light-danger"><i class="mx-2 feather icon-user-check"></i>Customer Notes</button></div>
                        </div>
                    </div>
                    <div class="border-bottom card-body">
                        <div class="row">
                            <div class="col-sm-auto mb-3 mb-sm-0">
                                <div class="d-sm-inline-block d-flex align-items-center"><img class="wid-60 img-radius mb-2" src="../assets/images/user/avatar-5.jpg" alt="Generic placeholder image ">
                                    <div class="ms-3 ms-sm-0 text-sm-center">
                                        <p><i class="material-icons-two-tone f-18 text-primary">thumb_up</i> 4</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row">
                                    <div class="col">
                                        <div class="">
                                            <h4 class="d-inline-block">You</h4><span class="badge bg-light-secondary">replied</span>
                                            <p class="text-muted">1 day ago on Wednesday at 8:18am</p>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-inline-block f-20 me-1"><a href="#" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit icon-svg-success wid-20">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg></a></li>
                                            <li class="d-inline-block f-20"><a href="#" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 icon-svg-danger wid-20">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                                    </svg></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="">
                                    <p><b>hello john doe,</b></p>
                                    <p>you need to create <strong>"toolbar-options" div only once</strong> in a page in your code, this div fill found <strong>every "td"</strong> tag in your page, just remove those things.</p>
                                    <p>and also, in option button add <strong>"p-0" class in "I" tag</strong> to</p>
                                    <p>Thanks...</p>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Ticket Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success d-block text-center text-uppercase"><i class="feather icon-check-circle mx-2"></i>Verified Purchase</div>
                        <div class="select-block">
                            <div class="mb-2"><select class="form-control col-sm-12">
                                    <option>Open</option>
                                    <option>Close</option>
                                    <option>CLosed Forever</option>
                                </select></div>
                            <div class="mb-2"><select class="form-control col-sm-12">
                                    <option value="avatar-5">Jack Pall</option>
                                    <option value="avatar-4">Liza Mac</option>
                                    <option value="avatar-3">Lina Hop</option>
                                    <option value="avatar-2">Sam Hunk</option>
                                    <option value="avatar-1">Jhon White</option>
                                </select></div>
                            <div class="mb-2"><select class="form-control col-sm-12">
                                    <option value="prod-1">Able Admin</option>
                                    <option value="prod-2">Guru Dash</option>
                                    <option value="prod-3">Able pro</option>
                                    <option value="prod-4">Able Dash</option>
                                    <option value="prod-5">Dash Able</option>
                                </select></div>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="media align-items-center"><label class="mb-0 wid-100">Customer</label>
                                <div class="media-body">
                                    <p class="mb-0"><img src="../assets/images/user/avatar-5.jpg" alt="" class="wid-20 rounded me-1 img-fluid"><a href="#" class="link-secondary">Jack Pall</a></p>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="media align-items-center"><label class="mb-0 wid-100">Contact</label>
                                <div class="media-body">
                                    <p class="mb-0"><i class="feather icon-mail mx-1"></i><a href="#" class="link-secondary">mail@mail.com</a></p>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="media align-items-center"><label class="mb-0 wid-100">Category</label>
                                <div class="media-body">
                                    <p class="mb-0"><img src="../assets/images/admin/p1.jpg" alt="" class="wid-20 rounded me-1 img-fluid"><a href="#" class="link-secondary">Alpha pro</a></p>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="media align-items-center"><label class="mb-0 wid-100">Assigned</label>
                                <div class="media-body">
                                    <p class="mb-0"><img src="../assets/images/user/avatar-4.jpg" alt="" class="wid-20 rounded me-1 img-fluid"><a href="#" class="link-secondary">Lina Hop</a></p>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="media align-items-center"><label class="mb-0 wid-100">Created</label>
                                <div class="media-body">
                                    <p class="mb-0"><i class="feather icon-calendar me-1"></i><label class="mb-0">Date</label></p>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="media align-items-center"><label class="mb-0 wid-100">Response</label>
                                <div class="media-body">
                                    <p class="mb-0"><i class="feather icon-clock me-1"></i><label class="mb-0">Time</label></p>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item py-3"><button type="button" class="btn btn-sm btn-light-warning me-2"><i class="mx-2 feather icon-thumbs-up"></i>Make Private</button> <button type="button" class="btn btn-sm btn-light-danger"><i class="mx-2 feather icon-trash-2"></i>Delete</button></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- [ Main Content ] end -->
<?php
include('footer.php')

?>

<script src="scripts/misevidencias.js"></script>