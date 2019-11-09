
// -- client routine --

function responseHandler(response, handler, callback) {
	if (response.readyState == 4)
		if (response.status == 200)
			if (response.responseText) {
//				alert(response.responseText);
				callback(handler(response.responseXML));
			} else
				alert("Communication error: No data received");
		else
			alert("Communication error: " + response.statusText);
}

function asyncRequest(phpUri, handler, callback) {
	let request = new XMLHttpRequest();
	request.open("POST", phpUri, true);
	request.onreadystatechange = function () { responseHandler(this, handler, callback); };
	return request;
}

function setRequestHeader(request, contLength) {
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//	request.setRequestHeader("Content-length", contLength);
//	request.setRequestHeader("Connection", "close");
}

function xmlErrorCheck(xmlDoc) {
	if (!xmlDoc) {
		alert("Server error: bad XML");
		return false;
	}
	let error = xmlDoc.getElementsByTagName("error")[0];
	if (error) {
		let errMsg = "Server error: ";
		if (error.childNodes.length > 0)
			errMsg += error.childNodes[0].nodeValue;
		else
			errMsg += "unknown";
		alert(errMsg);
		return false;
	}
	return true;
}

// -- response handlers --

function loginResponseHandler(xmlDoc) {
	if (!xmlErrorCheck(xmlDoc))
		return null;
	let tokenEl = xmlDoc.getElementsByTagName("token");
	if (tokenEl.length == 0) {
		alert("Server error: Bad response");
		var token = null;
	} else
		token = tokenEl[0].childNodes[0].nodeValue;
	return token;
}

function getMessagesResponseHandler(xmlDoc) {
	if (!xmlErrorCheck(xmlDoc))
		return null;

	let response = new Object;
	// parse users
	let users = xmlDoc.getElementsByTagName("user");
	let usersParsed = []
	for (let i = 0; i < users.length; i++)
		usersParsed.push(users[i].childNodes[0].nodeValue);
	response.users = usersParsed;
	
	//parse messages
	let msgs = xmlDoc.getElementsByTagName("msg");
	let msgsParsed = [];
	for (let i = 0; i < msgs.length; i++) {
		let msg = new Object;
		msg.id = msgs[i].getAttribute("id");
		msg.time = msgs[i].getAttribute("time");
		msg.userName = msgs[i].getAttribute("uname");
		msg.text = msgs[i].childNodes[0].nodeValue;
		msgsParsed.push(msg);
	}
	response.messages = msgsParsed;

	return response;
}

function sendMessageResponseHandler(xmlDoc) {
	if (!xmlErrorCheck(xmlDoc))
		return false;
	return true;
}

// -- client API --

function clientLogin(userName, callback) {
	let request = asyncRequest("./php/enterchat.php", loginResponseHandler, callback);
	let params = "name=" + encodeURIComponent(userName);
	setRequestHeader(request, params.length);
	request.send(params);
}

function clientGetMessages(userName, token, lastMsgId, callback) {
	let request = asyncRequest("./php/getmsgs.php", getMessagesResponseHandler, callback);
	let params = "name=" + encodeURIComponent(userName);
	params += "&token=" + token + "&last_msg_id=" + lastMsgId;
	setRequestHeader(request, params.length);
	request.send(params);
}

function clientSendMessage(userName, token, text, callback) {
	let request = asyncRequest("./php/sendmsg.php", sendMessageResponseHandler, callback);
	let params = "name=" + encodeURIComponent(userName);
	params += "&token=" + token;
	params += "&text=" + encodeURIComponent(text);
	setRequestHeader(request, params.length);
	request.send(params);
}
