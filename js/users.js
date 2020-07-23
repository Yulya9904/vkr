$(document).ready(function() {
    var usersData = $('#userList').DataTable({
        language: {
            "processing": "Подождите...",
            "search": "Поиск:",
            "lengthMenu": "Показать _MENU_ записей",
            "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
            "infoEmpty": "Записи с 0 до 0 из 0 записей",
            "infoFiltered": "(отфильтровано из _MAX_ записей)",
            "infoPostFix": "",
            "loadingRecords": "Загрузка записей...",
            "zeroRecords": "Записи отсутствуют.",
            "emptyTable": "В таблице отсутствуют данные",
            "paginate": {
                "first": "Первая",
                "previous": "Предыдущая",
                "next": "Следующая",
                "last": "Последняя"
            },
            "aria": {
                "sortAscending": ": активировать для сортировки столбца по возрастанию",
                "sortDescending": ": активировать для сортировки столбца по убыванию"
            }
        },
        "lengthChange": false,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "action.php",
            type: "POST",
            data: { action: 'listUser' },
            dataType: "json"
        },
        "columnDefs": [{
            "targets": [0, 5, 6],
            "orderable": false,
        }, ],
        "pageLength": 200,
        "bPaginate": false
    });
    console.log(usersData)
    $(document).on('click', '.delete', function() {
        var userid = $(this).attr("id");
        var action = "userDelete";
        if (confirm("Вы уверены, что хотите удалить этого пользователя?")) {
            $.ajax({
                url: "action.php",
                method: "POST",
                data: { userid: userid, action: action },
                success: function(data) {
                    usersData.ajax.reload();
                }
            })
        } else {
            return false;
        }
    });
    $('#addUser').click(function() {
        $('#userModal').modal('show');
        $('#userForm')[0].reset();
        $('#passwordSection').show();
        $('.modal-title').html("Добавить пользователя");
        // <i class='fa fa-plus'></i>
        $('#action').val('addUser');
        $('#save').val('Добавить');
    });
    $(document).on('click', '.update', function() {
        var userid = $(this).attr("id");
        var action = 'getUser';
        $.ajax({
            url: 'action.php',
            method: "POST",
            data: { userid: userid, action: action },
            dataType: "json",
            success: function(data) {
                $('#userModal').modal('show');
                $('#userid').val(data.id);
                $('#firstname').val(data.first_name);
                $('#lastname').val(data.last_name);
                $('#patronymic').val(data.patronymic);
                $('#email').val(data.email);
                $('#password').val(data.password);
                $('#passwordSection').hide();
                if (data.type == 'Редактор') {
                    $('#general').prop("checked", true);
                } else if (data.type == 'Администратор') {
                    $('#administrator').prop("checked", true);
                }
                // $('#mobile').val(data.mobile);
                // $('#designation').val(data.designation);
                $('.modal-title').html("Редактировать пользователя");
                // <i class='fa fa-plus'></i>
                $('#action').val('updateUser');
                $('#save').val('Сохранить изменения');
            }
        })
    });
    $(document).on('submit', '#userForm', function(event) {
        event.preventDefault();
        $('#save').attr('disabled', 'disabled');
        var formData = $(this).serialize();
        $.ajax({
            url: "action.php",
            method: "POST",
            data: formData,
            success: function(data) {
                $('#userForm')[0].reset();
                $('#userModal').modal('hide');
                $('#save').attr('disabled', false);
                usersData.ajax.reload();
            }
        })
    });
});