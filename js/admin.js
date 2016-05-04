/**
 * Created by Ivan on 2016/4/30.
 */
window.onload = function () {

};

function reset(uid) {
    post("reset", uid);
}

function del(uid) {
    post("delete", uid);
}

function next(page) {
    var temp = document.createElement("form");
    temp.action = "admin.php";
    temp.method = "get";
    temp.style.display = "none";
    var opt = document.createElement("input");
    opt.name = "page";
    opt.value = page;
    temp.appendChild(opt);
    document.body.appendChild(temp);
    temp.submit();
    return temp;
}

function post(action, uid) {
    var temp = document.createElement("form");
    temp.action = "User.php";
    temp.method = "get";
    temp.style.display = "none";
    var opt1 = document.createElement("input");
    opt1.name = "action";
    opt1.value = action;
    temp.appendChild(opt1);
    var opt2 = document.createElement("input");
    opt2.name = "uid";
    opt2.value = uid;
    temp.appendChild(opt2);
    document.body.appendChild(temp);
    temp.submit();
    return temp;
}