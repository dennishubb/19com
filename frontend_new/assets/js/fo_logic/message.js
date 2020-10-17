//===message module starts===
window.messagesTree = {}
window.selectedSortByItem = ''
var sortByItem = [
    {
        field: "message.like_count",
        sort: "desc"
    },
    {
        field: "message.created_at",
        sort: "desc"
    },
]
function getMessages(parentIds) {
    if (!parentIds)
        parentIds = []
    const promises = parentIds.map(parentId => {
        var data ={
            filter: [
            ],
            sort: sortByItem
        }
        function setData(resolve) {
            if (window.message_chatroom_id) {
                data.filter.push(
                    {
                        field: 'message.chatroom_id',
                        value: window.message_chatroom_id,
                        operator: '='
                    }
                )
                resolve()
            }
            else {
                setTimeout(() => setData(resolve), 1000)
            }
        }
        return new Promise((resolve, reject) => {
            setData(resolve)
        }).then(_ => 
            data.filter.push(
                {
                    field: 'parent_id',
                    value: parentId,
                    operator: '='
                },
                {
                    field: 'message.status',
                    value: 'approve',
                    operator: '='
                }
            )
        ).then(_ => 
            $.ajax({
                url:link + 'api/cn/message',
                type:'get',
                data
            })
        ).then(response => {
            return {
                data,
                response,
                parentId
            }
        })
        .then(response => { /*console.log("xp",data);*/ return response.response.data })
    })
    
    return Promise.all(promises).then(promiseResponses => {
        let messages = []
        //if (promiseResponses?.length){
        if (promiseResponses != null && promiseResponses.length){
            promiseResponses.forEach(promiseResponse => {
                messages = [...messages, ...promiseResponse]
            })
            return messages
        }
    })
}

function message() {
    var messagesTree = {
        messages: []
    }
    return getMessages(["0"])
    .then(function (messages) {
        messagesTree.messages = messages;
        return messages
    })
    .then(messages => {
        return getMessages(
            messages
                .map(message => message.id)
        )
    }).then(subMessages => {
        messagesTree.messages.forEach(message => {
           message.subMessages = subMessages.filter(subMessage => subMessage.parent_id === message.id)
        })
        return messagesTree
    })
    .then (messagesTree => {
         messagesTree.messages.forEach(function (eachMessage){
            eachMessage.selfComment = ""
            if(eachMessage.user_id == window.localStorage.user_id){
                eachMessage.selfComment = true
            }
            eachMessage.subMessages.forEach(function (eachSubMessage){
                eachSubMessage.selfComment = ""
                if(eachSubMessage.user_id == window.localStorage.user_id){
                    eachSubMessage.selfComment = true
                }
            })
        })
        return messagesTree
    })
    .then(messagesTree => {
        /*if (_.isEqual(messagesTree, window.messagesTree) && !window.changeMatch){
            ////console.log("12312",messagesTree)
            return messagesTree
        }*/
        //else {
            window.messagesTree = messagesTree
            ////console.log("45345",messagesTree)
            messagesTree.userPicture = window.localStorage.profile_thumbnail
            // html rendering
            return $.get('/cn/module/message.html').then(function (htmlTemplate) {
                var render = template.compile(htmlTemplate);
                var renderedHtml = render({
                    messagesTree
                })
                $('.comments').html(renderedHtml)
                if (window.selectedSortByItem) $("#sort-type").find(`option[value=${window.selectedSortByItem}]`).attr('selected', '1')
            }).then(_ => messagesTree)
        //}
    })
    //.then(tree => //console.log(tree))
}

function createNewMessage(main_message) {
    $(".report_pop").css("display", "none")
    if (check_auth(0)) return ""
    if(!main_message) return alert("请写下留言")

    message_data = {
        parent_id : "0",
        message : main_message,
        chatroom_id : message_chatroom_id
    }

    var create_message_func = $.ajax({
        type: "POST",
        url:link + 'api/cn/message',
        data: JSON.stringify(message_data),
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false,
        error: function () {
            alert('AJAX ERROR - create message');
        },
    });
    $.when(create_message_func).done(function () {
        message()
    });
}

function replyMessage(reply_message, parent_id, chatroom_id) {
    $(".report_pop").css("display", "none")
    if (check_auth(0)) return ""
    if(!reply_message) return alert("请写下留言")

    message_data = {
        parent_id : parent_id,
        message : reply_message,
        chatroom_id : chatroom_id
    }

    var reply_message_func = $.ajax({
        type: "POST",
        url:link + 'api/cn/message',
        data: JSON.stringify(message_data),
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false,
        error: function () {
            alert('AJAX ERROR - reply message');
        },
    });
    $.when(reply_message_func).done(function () {
        message()
    });
}

function cancel(id) {
    $(".report_pop").css("display", "none")
    if (check_auth(0)) return ""
    $('#message_input_'+id).val('');
}

function count(id) {
    $(".report_pop").css("display", "none")
    if (check_auth(0)) return ""
    var message_data = {
        message_id : id
    }
    var like_count_func = $.ajax({
        type: "POST",
        url:link + 'api/cn/message_like',
        data: JSON.stringify(message_data),
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false
    }).then(response => {
        if (response.status == 'failed') alert(response.message)
        message()
    }, error => {
        alert('AJAX ERROR - like_count');
    });
}

function reply(id) {
    $(".report_pop").css("display", "none")
    if (check_auth(0)) return ""
    var chatroom_id = ''
    var reply_message = $('#message_input_'+id).val();
    if (reply_message) {
        var parent_id = id;
        window.messagesTree.messages.forEach(function (message){
            if(message.id == id){
                chatroom_id = message.chatroom_id
            }
        })
    replyMessage(reply_message, parent_id, chatroom_id)
    }else {
        alert("请写下留言")
    }
}

var message_user_name = ""
function replyTo(id) {
    $(".report_pop").css("display", "none")
    if (check_auth(0)) return ""
    if (!id) return ""
    window.messagesTree.messages.forEach(function (message){
        if(message.id == id){
            message_user_name = message.user_username
        }
    })
    afterClickReply(id, message_user_name)
}

function replyToSub(id) {
    $(".report_pop").css("display", "none")
    if (check_auth(0)) return ""
    if (!id) return ""
    idAry = id.split("_");
    message_input_id = idAry[0]
    reply_id = idAry[1]
    window.messagesTree.messages.forEach(function (message){
        if(message.id == message_input_id){
            message.subMessages.forEach(function (subMessage){
                if(subMessage.id == reply_id) {
                    message_user_name = subMessage.user_username
                }
            })
        }
    })
    afterClickReply(message_input_id, message_user_name)
}
function afterClickReply(message_input_id, message_user_name) {
    /*$('html, body').animate({
        scrollTop: $('#message_input_'+message_input_id).offset().top
    }, 10);*/
    $('#message_input_'+message_input_id).focus();
    $('#message_input_'+message_input_id).val('@'+message_user_name);
}

function sortByFunc (value){
    if(value == 'created_at') {
        sortByItem = {
            field: "message.created_at",
            sort: "desc"
        }
    }else if (value == 'like_count'){
        sortByItem =[
            {
                field: "message.like_count",
                sort: "desc"
            },
            {
                field: "message.created_at",
                sort: "desc"
            },
        ]
    }
    window.selectedSortByItem = value
    $.when(sortByFunc).done(function () {
        message()
    });
}

function messageReport(messageId, reportType){
    if (check_auth(0)) return ""
    if(!messageId || !reportType) alert("举报错误！")
    var message_report = {
        message_id: messageId,
        report: reportType,
        user_id: window.localStorage.user_id
    }
    $.ajax({
        type: "POST",
        url:link + 'api/cn/message_report',
        data: JSON.stringify(message_report),
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.code == 1) {
                alert("举报成功！")
            }else {
                alert("举报失败！")
            }
        },
        error: function () {
            alert('AJAX ERROR - message_report');
        },
    });
}
var text = ""
var option = ""
var messageId = ""
var confirmMessage = ""

function main_message_cancel() {
    $(".report_pop").css("display", "none")
    if (check_auth(0)) return ""
    $('#main_message_input').val('');
}


function main_message_reply() {
    $(".report_pop").css("display", "none")
    if (check_auth(0)) return ""
    var main_message = $('#main_message_input').val();
    createNewMessage(main_message)
}

var activePopUp = false;
function reportBtn(item){
    if (check_auth(0)) return ""
    $("#report_pop_"+item.id).toggle();
    activePopUp = ".report_pop";
}

function report_type(item) {
    if (check_auth(0)) return ""
    text = $(item).text()
    messageId = (item.id).split("_")
    option = messageId[0]
    if (option){
        $(".report_pop").css("display", "none")
        /*confirmMessage = confirm("确认举报留言为"+text+"?")
        if(confirmMessage){
            messageReport(messageId[1],option)
        }*/
        confirm("确认举报留言为"+text+"?").then(result => {
            const confirmed = result.confirmed
            if(!confirmed){
                    return;
            }
            else {
                messageReport(messageId[1],option)
            }
        });
    }else return ""
}

function hideList() {
    if(activePopUp) {
        $(activePopUp).hide();
        activePopUp = false;
    }
}

function deleteSelfComment(deleteId) {
    //var confirmMessage = confirm("确定删除此信息？")
    confirm("确定删除此信息？").then(result => {
        const confirmed = result.confirmed
        if(!confirmed){
                return;
        }
        else {
            var splitId = deleteId.split("-")
            var deleteCommentId = splitId[1]
            var recycle_message_data = {
                id : deleteCommentId,
                status : "delete"
            }
            $.ajax({
                type: "PUT",
                url:link + 'api/cn/message',
                data: JSON.stringify(recycle_message_data),
                crossDomain: true,
                headers: getHeaders(),
                contentType: false,
                processData: false
            }).then(response => {
                if (response.status == 'failed') alert(response.message)
                message()
            }, error => {
                alert('AJAX ERROR - recycle self comment');
            });
        }
    });
}

//===message module ends===
