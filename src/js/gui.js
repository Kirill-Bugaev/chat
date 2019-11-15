
// -- global --

const historyUpdateTimeout = 1000; // msec
var userName = "";
var token = "";
var lastMsgId = 0;

// -- helpers --

function addLeadingZero(num) {
	let str = num.toString();
	return str.length == 2 ? str : "0" + str;
}

// -- elements config --

function onDocumentLoad() {
	setKeyListeners();
	document.getElementById("name-input").focus();
}

function setKeyListeners() {
	let nameInput = document.getElementById("name-input");
	nameInput.addEventListener("keyup", (event) => {
		if (event.keyCode == 13) {
			event.preventDefault();
			document.getElementById("enter-btn").click();
		}
	});

	let msgInput = document.getElementById("msg-input");
	msgInput.addEventListener("keyup", (event) => {
		if (event.keyCode == 13) {
			event.preventDefault();
			document.getElementById("send-btn").click();
		}
	});
}

// -- element events --

function enterBtnOnClick() {
	let userName = document.getElementById("name-input").value.trim();
	if (userName == "")
		return;
	enterChat(userName);
}

function sendBtnOnClick() {
	let text = document.getElementById("msg-input").value.trim();
	if (text == "")
		return;
	sendMessage(text);
}

// -- dynamic elements --

function showChat() {
	document.getElementById("login-form").style.display = "none";
	document.getElementById("chat-form").style.display = "block";
	document.getElementById("msg-input").focus();
}

function updateOnlineUsers(users) {
	let p = document.getElementById("users-online-p");
	let line = "";
	for (let i = 0; i < users.length; i++) {
		line += users[i];
		if (i != users.length - 1)
			line += ", "
	}
	p.innerHTML = "Users online: " + line;
}

function updateHistory(msgs) {
	let textarea = document.getElementById("chat-history-txtarea");
	let newText = "";
	for (let i = 0; i < msgs.length; i++) {
		let date = new Date(msgs[i].time);
		newText += addLeadingZero(date.getHours()) + ":" + addLeadingZero(date.getMinutes()) + " | ";
		newText += msgs[i].userName + ": ";
		newText += msgs[i].text + "\n";
	}
	textarea.value += newText;
	textarea.scrollTop = textarea.scrollHeight;
}

function clearMessageInput() {
	let input = document.getElementById("msg-input");
	input.value = "";
}

// -- client interaction --

function enterChat(userName) {
	clientLogin(userName, (token) => {
		if (!token)
			return;
		window.userName = userName;
		window.token = token;
		showChat();
		window.setTimeout(updateChat, historyUpdateTimeout);
	});
}

function updateChat() {
	clientGetChat(userName, token, lastMsgId, (response) => {
		if (response != null) {
			updateOnlineUsers(response.users);
			if (response.messages.length != 0 ) {
				window.lastMsgId = response.messages[response.messages.length - 1].id;
				updateHistory(response.messages);
			}
		}
		window.setTimeout(updateChat, historyUpdateTimeout);
	});
}

function sendMessage(text) {
	clientSendMessage(userName, token, text, (result) => {
		if (result)
			clearMessageInput();
	});
}
