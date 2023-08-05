// CODEIGNITER 3 : CSRF same as in Application/config/config.php.
let csrf_token_name = 'cid';
let csrf_cookie_name = 'ccookie';
let localeMapCurrency = {
	USD: {
		symbol: '$',
		pattern: '$ #,##0.00',
		code: 'en-US'
	}, // United States Dollar (USD)
	JPY: {
		symbol: '¥',
		pattern: '¥ #,##0',
		code: 'ja-JP'
	}, // Japanese Yen (JPY)
	GBP: {
		symbol: '£',
		pattern: '£ #,##0.00',
		code: 'en-GB'
	}, // British Pound Sterling (GBP)
	EUR: {
		symbol: '€',
		pattern: '€ #,##0.00',
		code: 'en-GB'
	}, // Euro (EUR) - Using en-GB for Euro
	AUD: {
		symbol: 'A$',
		pattern: 'A$ #,##0.00',
		code: 'en-AU'
	}, // Australian Dollar (AUD)
	CAD: {
		symbol: 'C$',
		pattern: 'C$ #,##0.00',
		code: 'en-CA'
	}, // Canadian Dollar (CAD)
	CHF: {
		symbol: 'CHF',
		pattern: 'CHF #,##0.00',
		code: 'de-CH'
	}, // Swiss Franc (CHF)
	CNY: {
		symbol: '¥',
		pattern: '¥ #,##0.00',
		code: 'zh-CN'
	}, // Chinese Yuan (CNY)
	SEK: {
		symbol: 'kr',
		pattern: 'kr #,##0.00',
		code: 'sv-SE'
	}, // Swedish Krona (SEK)
	MYR: {
		symbol: 'RM',
		pattern: 'RM #,##0.00',
		code: 'ms-MY'
	}, // Malaysian Ringgit (MYR)
	SGD: {
		symbol: 'S$',
		pattern: 'S$ #,##0.00',
		code: 'en-SG'
	}, // Singapore Dollar (SGD)
	INR: {
		symbol: '₹',
		pattern: '₹ #,##0.00',
		code: 'en-IN'
	}, // Indian Rupee (INR)
	IDR: {
		symbol: 'Rp',
		pattern: 'Rp #,##0',
		code: 'id-ID'
	}, // Indonesian Rupiah (IDR)
};

// CUSTOM HELPER

const log = (...args) => {
	args.forEach((param) => {
		console.log(param);
	});
}

const dd = (...args) => {
	args.forEach((param) => {
		console.log(param);
	});
	throw new Error("Execution terminated by dd()");
}

const loadingBtn = (id, display = false, text = "<i class='ti ti-device-floppy ti-xs mb-1'></i> Save") => {
	if (display) {
		$("#" + id).html('Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>');
		$("#" + id).attr('disabled', true);
	} else {
		$("#" + id).html(text);
		$("#" + id).attr('disabled', false);
	}
}

const printDiv = (idToPrint, printBtnID = 'printBtn', printBtnText = "<i class='ti ti-device-floppy ti-xs mb-1'></i> Save", pageTitlePrint = 'Print') => {
	$("#" + idToPrint).printThis({
		// header: $('#headerPrint').html(),
		// footer: $('#tablePrint').html(), 
		importCSS: false,
		pageTitle: pageTitlePrint,
		beforePrint: loadingBtn(printBtnID, true),
	});

	setTimeout(function () {
		loadingBtn(printBtnID, false, printBtnText);
		$('#' + idToPrint).empty(); // reset
	}, 800);
}

const disableBtn = (id, display = true, text = null) => {
	$("#" + id).attr("disabled", display);
}

const isNumberKey = (evt) => {
	var charCode = (evt.which) ? evt.which : evt.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
	return true;
}

const SizeToText = (size) => {
	var sizeContext = ["B", "KB", "MB", "GB", "TB"],
		atCont = 0;

	while (size / 1024 > 1) {
		size /= 1024;
		++atCont;
	}

	return Math.round(size * 100) / 100 + ' ' + sizeContext[atCont];
}

const loading = (id = null, display = false) => {
	if (display) {
		$(id).block({
			// message: '<div class="d-flex justify-content-center"> <div class="spinner-border text-light" role="status"></div> </div>',
			message: '<div class="d-flex justify-content-center"><p class="mb-0">Please wait...</p> <div class="sk-wave m-0"><div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div></div> </div>',
			css: {
				backgroundColor: 'transparent',
				color: '#fff',
				border: '0'
			},
			overlayCSS: {
				opacity: 0.15
			}
		});
	} else {
		setTimeout(function () {
			$(id).unblock();
		}, 80);
	}
}

const chunkData = (dataArr, perChunk) => {
	if (perChunk <= 0) perChunk = 15;

	let result = [];
	for (let i = 0; i < dataArr.length / perChunk; i++) {
		result.push(dataArr.slice(i * perChunk, i * perChunk + perChunk));
	}
	return result;

}

const chunkDataObj = (dataArr, chunk_size) => {
	if (chunk_size <= 0) chunk_size = 15;

	const chunks = [];
	for (const cols = Object.entries(dataArr); cols.length;)
		chunks.push(cols.splice(0, chunk_size).reduce((o, [k, v]) => (o[k] = v, o), {}));

	return chunks;

}

const getDataPerChunk = (total, percentage = 10) => {
	var percent = (percentage / 100) * total;
	return Math.round(percent);
}

// GENERAL HELPER

const ucfirst = (string) => {
	return string.charAt(0).toUpperCase() + string.slice(1);
}

const capitalize = (str) => {
	return str.toLowerCase().split(' ').map(function (word) {
		return word.replace(word[0], word[0].toUpperCase());
	}).join(' ');
}

const uppercase = (obj) => {
	obj.value = obj.value.toUpperCase();
	return obj.value;
}

const distinct = (value, index, self) => {
	return self.indexOf(value) === index;
}

const random = (min, max) => {
	Math.floor(Math.random() * (max - min)) + min;
};

const isUndef = (value) => {
	return typeof value === undefined || value === null;
}

const isDef = (value) => {
	return typeof value !== undefined && value !== null;
}

const isTrue = (value) => {
	return value === true;
}

const isFalse = (value) => {
	return value === false;
}

const isObject = (obj) => {
	return obj !== null && typeof obj === 'object';
}

const isValidArrayIndex = (val) => {
	var n = parseFloat(String(val));
	return n >= 0 && Math.floor(n) === n && isFinite(val);
}

const isPromise = (val) => {
	return (
		isDef(val) &&
		typeof val.then === 'function' &&
		typeof val.catch === 'function'
	);
}

const isArray = (val) => {
	return Array.isArray(val) ? true : false;
}

const isMobileJs = () => {
	const toMatch = [
		/Android/i,
		/webOS/i,
		/iPhone/i,
		/iPad/i,
		/iPod/i,
		/BlackBerry/i,
		/Windows Phone/i
	];

	return toMatch.some((toMatchItem) => {
		return navigator.userAgent.match(toMatchItem);
	});
}

const maxLengthCheck = (object) => {
	if (object.value.length > object.maxLength)
		object.value = object.value.slice(0, object.maxLength)
}

const isNumeric = (evt) => {
	var theEvent = evt || window.event;
	var key = theEvent.keyCode || theEvent.which;
	key = String.fromCharCode(key);
	var regex = /[0-9]|\./;
	if (!regex.test(key)) {
		theEvent.returnValue = false;
		if (theEvent.preventDefault) theEvent.preventDefault();
	}
}

const isDigit = (str) => {
	var regex = /[0-9]|\./;
	return regex.test(str);
}

const jsonHtmlHighlight = (json) => {
	if (typeof json != 'string') {
		json = JSON.stringify(json, undefined, 2);
	}
	json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
	return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
		var cls = 'number';
		if (/^"/.test(match)) {
			if (/:$/.test(match)) {
				cls = 'key';
			} else {
				cls = 'string';
			}
		} else if (/true|false/.test(match)) {
			cls = 'boolean';
		} else if (/null/.test(match)) {
			cls = 'null';
		}
		return '<span class="' + cls + '">' + match + '</span>';
	});
}

// URL & ASSET HELPER

const base_url = () => {
	return $('meta[name="base_url"]').attr('content');
}

const urls = (path) => {
	const newPath = new URL(path, base_url());
	return newPath.href;
}

const redirect = (url) => {
	const pathUrl = base_url() + url;
	window.location.replace(pathUrl);
	// window.location.href = pathUrl;
}

const refreshPage = () => {
	location.reload();
}

const asset = (path, isPublic = true) => {
	const publicFolder = isPublic ? 'public/' : '';
	return urls(publicFolder + path);
}

// MODAL (BOOTSTRAP) HELPER

const showModal = (id, timeSet = 0) => {
	setTimeout(function () {
		$(id).modal('show');
	}, timeSet);
}

const closeModal = (id, timeSet = 250) => {
	setTimeout(function () {
		$(id).modal('hide');
	}, timeSet);
}

const closeOffcanvas = (id, timeSet = 250) => {
	setTimeout(function () {
		$(id).offcanvas('toggle');
	}, timeSet);
}

// DATA HELPER

const isset = (variable_name) => {
	if (isDef(variable_name)) {
		return true;
	}

	return false;
}

const hasData = (data = null, arrKey = null, returnData = false, defaultValue = null) => {
	let response = false; // default return

	// check if data is exist
	if (isset(data) && data !== '' && data !== 'null') {
		response = true;

		// check if arrKey is exist and not null
		if (isset(arrKey) && arrKey !== '') {
			const keys = arrKey.split('.');
			const keyArr = keys[0];

			if (keys.length === 1) {
				response = array_key_exists(keyArr, data) ? true : false;
			} else {
				const remainingKeys = keys.slice(1).join('.');
				if (array_key_exists(keyArr, data)) {
					response = hasData(data[keyArr], remainingKeys, returnData, defaultValue);
				}
			}
		}
	}

	// if return data is set to true it will return the data instead of bool
	if (returnData) {
		return response && isset(arrKey) ? stringToNestedArray(arrKey, data, defaultValue) : (response ? data : defaultValue);
	}

	return response;
};

const stringToNestedArray = (string, data, defaultValue = null, separator = '.') => {
	const keys = string.split(separator);
	const result = keys.reduce((obj, key) => obj !== undefined && obj !== null ? obj[key] : defaultValue, data);
	return result === undefined ? defaultValue : result;
};

const trimData = (text = null) => {
	if (hasData(text))
		return typeof text === 'string' ? text.trim() : text;
	else
		return null;
}

// ARRAY HELPER

const array_push = (arrayData, ...elements) => {
	return arrayData.push(...elements);
}

const array_merge = (...arrays) => {
	return [].concat(...arrays);
}

const implode = (separator = null, arrayData = null) => {
	return arrayData.join(separator);
}

const explode = (delimiter = null, string = null) => {
	return string.split(delimiter);
}

const remove = (arrayData, item) => {
	if (arrayData.length) {
		var index = arrayData.indexOf(item);
		if (index > -1) {
			return arrayData.splice(index, 1)
		}
	}
}

const array_key_exists = (arrKey = null, dataObj = null) => {
	if (hasData(dataObj) && hasData(arrKey)) {
		if (dataObj.hasOwnProperty(arrKey)) {
			return true;
		} else {
			return false;
		}
	}

	return false;
}

// DATE & TIME HELPER

const isWeekend = (date = new Date()) => {
	return date.getDay() === 6 || date.getDay() === 0;
}

const getCurrentTime = () => {
	var today = new Date();
	var hh = today.getHours();

	if (hh < 10) {
		hh = '0' + hh
	}
	return hh + ":" + today.getMinutes() + ":" + today.getSeconds();
}

const getCurrentDate = () => {
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth() + 1; //January is 0 so need to add 1 to make it 1!
	var yyyy = today.getFullYear();
	if (dd < 10) {
		dd = '0' + dd
	}
	if (mm < 10) {
		mm = '0' + mm
	}

	return yyyy + '-' + mm + '-' + dd;
}

// CURRENCY HELPER

const formatCurrency = (value, code = null, includeSymbol = false) => {
	// Check if the "Intl" object is available in the browser
	if (typeof Intl === 'undefined' || typeof Intl.NumberFormat === 'undefined') {
		return 'Error: The "Intl" object is not available in this browser, which is required for number formatting.';
	}

	if (!localeMapCurrency.hasOwnProperty(code)) {
		return 'Error: Invalid country code.';
	}

	// Validate the includeSymbol parameter
	if (typeof includeSymbol !== 'boolean') {
		return 'Error: includeSymbol parameter must be a boolean value.';
	}

	const currencyData = localeMapCurrency[code];

	const formatter = new Intl.NumberFormat(currencyData.code, {
		style: 'decimal',
		useGrouping: true,
		minimumFractionDigits: 2,
		maximumFractionDigits: 2,
	});

	if (includeSymbol) {
		const symbolFormatter = new Intl.NumberFormat(currencyData.code, {
			style: 'currency',
			currency: code,
			minimumFractionDigits: 2,
			maximumFractionDigits: 2,
		});
		return symbolFormatter.format(value);
	}

	return formatter.format(value);
};

const currencySymbol = (currencyCode = null) => {
	if (!localeMapCurrency.hasOwnProperty(currencyCode)) {
		return 'Error: Invalid country code.';
	}

	return localeMapCurrency[currencyCode]['symbol'];
};

// API CALLBACK HELPER 

const loginApi = async (url, dataObj, formID = null) => {
	const submitBtnText = $('#loginBtn').html();

	var btnSubmitIDs = $('#' + formID + ' button[type=submit]').attr("id");
	var inputSubmitIDs = $('#' + formID + ' input[type=submit]').attr("id");
	var submitIdBtn = isDef(btnSubmitIDs) ? btnSubmitIDs : isDef(inputSubmitIDs) ? inputSubmitIDs : null;

	loadingBtn(submitIdBtn, true, submitBtnText);

	if (dataObj != null) {
		url = urls(url);

		try {
			var frm = $('#' + formID);
			const dataArr = new FormData(frm[0]);

			dataArr.append(csrf_token_name, Cookies.get(csrf_cookie_name)); // csrf

			return axios({
					method: 'POST',
					headers: {
						"Authorization": `Bearer ${Cookies.get(csrf_cookie_name)}`,
						'X-Requested-With': 'XMLHttpRequest',
						'content-type': 'application/x-www-form-urlencoded',
						"X-CSRF-TOKEN": Cookies.get(csrf_cookie_name),
					},
					url: url,
					data: dataArr
				})
				.then(result => {
					loadingBtn(submitIdBtn, false, submitBtnText);
					return result;
				})
				.catch(error => {

					log('ERROR 1 LOGIN');
					let textMessage = isset(error.response.data.message) ? error.response.data.message : error.response.statusText;

					if (isError(error.response.status)) {
						noti(error.response.status, textMessage);
					} else if (isUnauthorized(error.response.status)) {
						noti(error.response.status, "Unauthorized: Access is denied");
					}

					loadingBtn(submitIdBtn, false, submitBtnText);

					return error.response;

				});
		} catch (e) {
			const res = e.response;
			log(res, 'ERROR 2 LOGIN');

			loadingBtn(submitIdBtn, false, submitBtnText);

			if (isUnauthorized(res.status)) {
				noti(res.status, "Unauthorized: Access is denied");
			} else {
				if (isError(res.status)) {
					var error_count = 0;
					for (var error in res.data.errors) {
						if (error_count == 0) {
							noti(res.status, res.data.errors[error][0]);
						}
						error_count++;
					}
				} else {
					noti(res.status, 'Something went wrong');
				}
				return res;
			}
		}
	} else {
		loadingBtn(submitIdBtn, false, submitBtnText);
	}
}

const uploadApi = async (url, formID = null, idProgressBar = null, reloadFunction = null, permissions = null) => {
	try {
		url = urls(url);
		var frm = $('#' + formID);
		const dataArr = new FormData(frm[0]);

		dataArr.append(csrf_token_name, Cookies.get(csrf_cookie_name)); // csrf

		// console.log('uploadApi Data : ', ...dataArr);

		var timeStarted = new Date().getTime();

		let axiosConfig = {
			headers: {
				"Authorization": `Bearer ${Cookies.get(csrf_cookie_name)}`,
				'X-Requested-With': 'XMLHttpRequest',
				'content-type': 'multipart/form-data',
				"X-CSRF-TOKEN": Cookies.get(csrf_cookie_name),
				'X-Permission': permissions,
			},
			onUploadProgress: function (progressEvent) {
				if (idProgressBar != null) {
					const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);

					$('#' + idProgressBar).html(`
						<div class="col-12 mt-2 progress">
						<div id="componentProgressBarCanthink" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="col-12 mt-2 mb-4">
						<div id="componentProgressBarStatusCanthink"></div>
						</div>
					`);

					$('#componentProgressBarCanthink').width(percentCompleted + '%');

					const disSize = SizeToText(progressEvent.total);
					const progress = progressEvent.loaded / progressEvent.total;
					const timeSpent = new Date().getTime() - timeStarted;
					const secondsRemaining = Math.round(((timeSpent / progress) - timeSpent) / 1000);

					let time;
					if (secondsRemaining >= 3600) {
						time = `${Math.floor(secondsRemaining / 3600)} hour ${Math.floor((secondsRemaining % 3600) / 60)} minute`;
					} else if (secondsRemaining >= 60) {
						time = `${Math.floor(secondsRemaining / 60)} minute ${secondsRemaining % 60} second`;
					} else {
						time = `${secondsRemaining} second(s)`;
					}

					$('#componentProgressBarStatusCanthink').html(`${SizeToText(progressEvent.loaded)} of ${disSize} | ${percentCompleted}% uploading <br> estimated time remaining: ${time}`);

					if (percentCompleted == 100) {
						$("#componentProgressBarCanthink").addClass("bg-success").removeClass("bg-info");
						setTimeout(function () {
							$('#componentProgressBarCanthink').width('0%');
							$('#componentProgressBarStatusCanthink').empty();
							$('#' + idProgressBar).empty();
						}, 500);
					} else if (percentCompleted > 0 && percentCompleted <= 40) {
						$("#componentProgressBarCanthink").addClass("bg-danger");
					} else if (percentCompleted > 40 && percentCompleted <= 60) {
						$("#componentProgressBarCanthink").addClass("bg-warning").removeClass("bg-danger");
					} else if (percentCompleted > 60 && percentCompleted <= 99) {
						$("#componentProgressBarCanthink").addClass("bg-info").removeClass("bg-warning");
					}
				}
			}
		};

		return axios.post(url, dataArr, axiosConfig)
			.then(function (res) {

				if (reloadFunction != null) {
					reloadFunction();
				}

				return res;
			})
			.catch(function (error) {
				if (error.response) {
					// Request made and server responded
					if (isError(error.response.status)) {
						noti(error.response.status, 'Something went wrong');
					} else if (isUnauthorized(error.response.status)) {
						noti(error.response.status, "Unauthorized: Access is denied");
					}
				} else if (error.request) {
					// The request was made but no response was received
					noti(400, 'Something went wrong');
				} else {
					// Something happened in setting up the request that triggered an Error
					log(error.message, 'ERROR Upload Api');
					noti(400, 'Something went wrong');
				}

				// throw err;
			});

	} catch (e) {

		const res = e.response;
		log(e, 'ERROR Upload Api');
		log(res.status, 'ERROR Upload Api status');
		log(res.message, 'ERROR Upload Api message');

		if (isUnauthorized(res.status)) {
			noti(res.status, "Unauthorized: Access is denied");
		} else {
			noti(res.status, 'Something went wrong');
		}
	}
}

const submitApi = async (url, dataObj, formID = null, reloadFunction = null, permissions = null, closedModal = true) => {
	const submitBtnText = $('#submitBtn').html();

	var btnSubmitIDs = $('#' + formID + ' button[type=submit]').attr("id");
	var inputSubmitIDs = $('#' + formID + ' input[type=submit]').attr("id");
	var submitIdBtn = isDef(btnSubmitIDs) ? btnSubmitIDs : isDef(inputSubmitIDs) ? inputSubmitIDs : null;

	loadingBtn(submitIdBtn, true, submitBtnText);

	if (dataObj != null) {
		url = urls(url);

		try {
			var frm = $('#' + formID);
			const dataArr = new FormData(frm[0]);

			dataArr.append(csrf_token_name, Cookies.get(csrf_cookie_name)); // csrf

			return axios({
					method: 'POST',
					headers: {
						"Authorization": `Bearer ${Cookies.get(csrf_cookie_name)}`,
						'X-Requested-With': 'XMLHttpRequest',
						'content-type': 'application/x-www-form-urlencoded',
						"X-CSRF-TOKEN": Cookies.get(csrf_cookie_name),
						'X-Permission': permissions,
					},
					url: url,
					data: dataArr
				})
				.then(result => {

					if (isSuccess(result.status) && reloadFunction != null) {
						reloadFunction();
					}

					if (formID != null) {
						if (closedModal) {
							var modalID = $('#' + formID).attr('data-modal');
							setTimeout(function () {
								if (modalID == '#generaloffcanvas-right') {
									$(modalID).offcanvas('toggle');
								} else {
									// $('#' + modalID).modal('hide');
									$(modalID).modal('hide');
								}
							}, 350);
						}
					}

					loadingBtn(submitIdBtn, false, submitBtnText);
					return result;

				})
				.catch(error => {

					log('ERROR SubmitApi 1');
					let textMessage = isset(error.response.data.message) ? error.response.data.message : error.response.statusText;

					if (isError(error.response.status)) {
						noti(error.response.status, textMessage);
					} else if (isUnauthorized(error.response.status)) {
						noti(error.response.status, "Unauthorized: Access is denied");
					} else {
						log(error, 'Response Submit Api 1');
					}

					return error.response;

				});
		} catch (e) {
			const res = e.response;
			log(res, 'ERROR 2 Submit');

			loadingBtn(submitIdBtn, false);

			if (isUnauthorized(res.status)) {
				noti(res.status, "Unauthorized: Access is denied");
			} else {
				if (isError(res.status)) {
					var error_count = 0;
					for (var error in res.data.errors) {
						if (error_count == 0) {
							noti(res.status, res.data.errors[error][0]);
						}
						error_count++;
					}
				} else {
					noti(res.status, 'Something went wrong');
				}
				return res;
			}
		}
	} else {
		noti(400, "No data to insert!");
		loadingBtn('submitBtn', false);
	}
}

const deleteApi = async (id, url, reloadFunction = null, permissions = null) => {
	if (id != '') {
		url = urls(url + '/' + id);
		try {
			return axios({
					method: 'DELETE',
					headers: {
						"Authorization": `Bearer ${Cookies.get(csrf_cookie_name)}`,
						'X-Requested-With': 'XMLHttpRequest',
						'content-type': 'application/x-www-form-urlencoded',
						"X-CSRF-TOKEN": Cookies.get(csrf_cookie_name),
						'X-Permission': permissions,
					},
					url: url,
				})
				.then(result => {
					if (isSuccess(result.status) && reloadFunction != null) {
						reloadFunction();
					}
					noti(result.status, 'Remove');
					return result;
				})
				.catch(error => {

					log('ERROR DeleteApi 1');
					let textMessage = isset(error.response.data.message) ? error.response.data.message : error.response.statusText;

					if (isError(error.response.status)) {
						noti(error.response.status, textMessage);
					} else if (isUnauthorized(error.response.status)) {
						noti(error.response.status, "Unauthorized: Access is denied");
					} else {
						log(error, 'Response Delete Api 1');
					}

					return error.response;

				});
		} catch (e) {
			const res = e.response;
			log(e, 'Response Delete Api 2');

			if (isUnauthorized(res.status)) {
				noti(res.status, "Unauthorized: Access is denied");
			} else {
				if (isError(res.status)) {
					var error_count = 0;
					for (var error in res.data.errors) {
						if (error_count == 0) {
							noti(res.status, res.data.errors[error][0]);
						}
						error_count++;
					}
				} else {
					noti(500, 'Something went wrong');
				}
				return res;
			}
		}
	} else {
		noti(400);
	}
}

const callApi = async (method = 'POST', url, dataObj = null, permissions = null) => {
	url = urls(url);
	let dataSent = null;

	if (method == 'post' || method == 'put') {
		dataObj[csrf_token_name] = Cookies.get(csrf_cookie_name) // csrf token
		dataSent = new URLSearchParams(dataObj);
	}

	try {
		return axios({
					method: method,
					headers: {
						"Authorization": `Bearer ${Cookies.get(csrf_cookie_name)}`,
						'X-Requested-With': 'XMLHttpRequest',
						'content-type': 'application/x-www-form-urlencoded',
						"X-CSRF-TOKEN": Cookies.get(csrf_cookie_name),
						'X-Permission': permissions,
					},
					url: url,
					data: dataSent,
				},
				// option
			).then(result => {
				return result;
			})
			.catch(error => {
				log('ERROR CallApi 1');
				let textMessage = isset(error.response.data.message) ? error.response.data.message : error.response.statusText;

				if (isError(error.response.status)) {
					noti(error.response.status, textMessage);
				} else if (isUnauthorized(error.response.status)) {
					noti(error.response.status, "Unauthorized: Access is denied");
				} else {
					log(error, 'ERROR CallApi 1');
				}

				return error.response;
			});
	} catch (e) {
		log('ERROR CallApi 2');
		const res = e.response;
		if (isUnauthorized(res.status)) {
			noti(res.status, "Unauthorized: Access is denied");
		} else {
			if (isError(res.status)) {
				// var error_count = 0;
				// for (var error in res.data.errors) {
				// 	if (error_count == 0) {
				// 		noti(500, res.data.errors[error][0]);
				// 	}
				// 	error_count++;
				// }
				noti(res.response.status, res.response.data.message);
			} else {
				noti(500, 'Something went wrong');
			}
			return res;
		}
	}
}

const noti = (code = 400, text = 'Something went wrong') => {

	const apiStatus = {
		200: 'OK',
		201: 'Created', // POST/PUT resulted in a new resource, MUST include Location header
		202: 'Accepted', // request accepted for processing but not yet completed, might be disallowed later
		204: 'No Content', // DELETE/PUT fulfilled, MUST NOT include message-body
		301: 'Moved Permanently', // The URL of the requested resource has been changed permanently
		304: 'Not Modified', // If-Modified-Since, MUST include Date header
		400: 'Bad Request', // malformed syntax
		401: 'Unauthorized', // Indicates that the request requires user authentication information. The client MAY repeat the request with a suitable Authorization header field
		403: 'Forbidden', // unauthorized
		404: 'Not Found', // request URI does not exist
		405: 'Method Not Allowed', // HTTP method unavailable for URI, MUST include Allow header
		415: 'Unsupported Media Type', // unacceptable request payload format for resource and/or method
		426: 'Upgrade Required',
		429: 'Too Many Requests',
		451: 'Unavailable For Legal Reasons', // REDACTED
		500: 'Internal Server Error', // all other errors
		501: 'Not Implemented', // (currently) unsupported request method
		503: 'Service Unavailable' // The server is not ready to handle the request.
	};

	var resCode = typeof code === 'number' ? code : code.status;
	var textResponse = apiStatus[code];

	var messageText = isSuccess(resCode) ? ucfirst(text) + ' successfully' : isUnauthorized(resCode) ? 'Unauthorized: Access is denied' : isError(resCode) ? text : 'Something went wrong';
	var type = isSuccess(code) ? 'success' : 'error';
	var title = isSuccess(code) ? 'Great!' : 'Ops!';

	toastr.options = {
		"debug": false,
		"closeButton": !isMobileJs(),
		"newestOnTop": true,
		"progressBar": !isMobileJs(),
		"positionClass": !isMobileJs() ? "toast-top-right" : "toast-bottom-full-width",
		"preventDuplicates": isMobileJs(),
		"onclick": null,
		"showDuration": "300",
		"hideDuration": "1000",
		"timeOut": "5000",
		"extendedTimeOut": "1000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	}

	Command: toastr[type](messageText, title)
}

const isSuccess = (res) => {
	const successStatus = [200, 201, 302];
	const status = typeof res === 'number' ? res : res.status;
	return successStatus.includes(status);
}

const isError = (res) => {
	const errorStatus = [400, 404, 422, 429, 500, 503];
	const status = typeof res === 'number' ? res : res.status;
	return errorStatus.includes(status);
}

const isUnauthorized = (res) => {
	const unauthorizedStatus = [401, 403];
	const status = typeof res === 'number' ? res : res.status;
	return unauthorizedStatus.includes(status);
}

//  BASE64-ENCODING HELPER

const getImageSizeBase64 = (base64, type = 'b') => {

	var decodedData = atob(base64.split(',')[1]);
	var dataSizeInBytes = decodedData.length;
	var dataSizeInKB = (dataSizeInBytes / 1024).toFixed(2);
	var dataSizeInMB = (dataSizeInKB / 1024).toFixed(2);

	if (type == 'b' || type == 'B')
		return dataSizeInBytes;
	else if (type == 'kb' || type == 'KB')
		return dataSizeInKB;
	else if (type == 'mb' || type == 'MB')
		return dataSizeInMB;
}

// PROJECT BASED HELPER

const noSelectDataLeft = (text = 'Type', filesName = '5.png') => {

	var fileImage = $('meta[name="base_url"]').attr('content') + 'public/custom/images/nodata/' + filesName;

	return "<div id='nodataSelect' class='col-lg-12 mb-4 mt-2'>\
            <center>\
                <img src='" + fileImage + "' class='img-fluid mb-3' width='38%'>\
                <h3 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;margin-bottom:15px'> \
                	<strong> NO " + text.toUpperCase() + " SELECTED </strong>\
                </h3>\
				<h6 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;font-size: 13px;'> \
					Select any " + text + " on the left\
				</h6>\
			</center>\
            </div>";
}

const nodata = (text = true, filesName = '4.png') => {

	var fileImage = $('meta[name="base_url"]').attr('content') + 'public/custom/images/nodata/' + filesName;
	var showText = (text) ? '' : 'style="display:none"';
	var suggestion = (text) ? '' : '"display:none!important"';

	return "<div id='nodata' class='col-lg-12 mb-4 mt-2'>\
            <center>\
                <img src='" + fileImage + "' class='img-fluid mb-3' width='38%'>\
                <h3 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;margin-bottom:15px'> \
                <strong> NO INFORMATION FOUND </strong>\
                </h3>\
                <h6 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;font-size: 13px;" + suggestion + "'> \
                    Here are some action suggestions for you to try :- \
                </h6>\
            </center>\
            <div class='row d-flex justify-content-center w-100' " + showText + ">\
                <div class='col-lg m-1 text-left' style='max-width: 350px !important;letter-spacing :1px; font-family: Quicksand, sans-serif !important;font-size: 12px;'>\
                    1. Try the registrar function (if any).<br>\
                    2. Change your word or search selection.<br>\
                    3. Contact the system support immediately.<br>\
                </div>\
            </div>\
            </div>";
}

const nodataAccess = (filesName = '403.png') => {

	var fileImage = $('meta[name="base_url"]').attr('content') + 'public/custom/images/nodata/' + filesName;
	return "<div id='nodataAccess' class='col-lg-12 mb-4 mt-2'>\
            <center>\
                <img src='" + fileImage + "' class='img-fluid mb-3' width='30%'>\
                <h3 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;margin-bottom:15px'> \
                <strong> NO INFORMATION FOUND </strong>\
                </h3>\
            </center>\
            </div>";
}

const skeletonTable = (hasButton = true, hasFilter = null, buttonRefresh = true) => {

	let totalData = 3;
	let body = '';

	for (let index = 0; index < totalData; index++) {
		body += '<tr>\
					<td width="5%" class="skeleton"> </td>\
					<td width="31%" class="skeleton"> </td>\
					<td width="25%" class="skeleton"> </td>\
					<td width="25%" class="skeleton"> </td>\
					<td width="14%" class="skeleton"> </td>\
				</tr>';
	}

	let filters = '';
	if (hasData(hasFilter)) {
		for (let index = 0; index < hasFilter; index++) {
			filters += '<select class="form-control form-control-sm float-end me-2 skeleton" style="width: 12%!important;"></select>';
		}
	}

	let buttonAdd = '';
	if (isTrue(hasButton)) {
		buttonAdd = '<button type="button" class="btn btn-default btn-sm float-end skeleton">  &nbsp;&nbsp;&nbsp; </button>';
	}

	let buttonShow = buttonRefresh ? '<div class="col-xl-12 mb-4">\
										' + buttonAdd + '\
										<button type="button" class="btn btn-default btn-sm float-end me-2 skeleton">\
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
										</button>\
										' + filters + '\
										</div><br><br><br>' : '';

	return buttonShow + '<div class="col-xl-12 mt-2">\
				<button type="button" class="btn btn-default btn-sm skeleton">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </button>\
				<button type="button" class="btn btn-default btn-sm float-end skeleton mb-3">\
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
				</button>\
				<table class="table">\
					<tbody>' + body + '</tbody>\
				</table>\
			</div>';
}

const skeletonTableCard = (hasFilter = null, buttonRefresh = true) => {

	let totalData = random(5, 20);
	let body = '';

	for (let index = 0; index < totalData; index++) {
		body += '<tr>\
					<td width="5%" class="skeleton"> </td>\
					<td width="31%" class="skeleton"> </td>\
					<td width="25%" class="skeleton"> </td>\
					<td width="25%" class="skeleton"> </td>\
					<td width="14%" class="skeleton"> </td>\
				</tr>';
	}

	let filters = '';
	if (hasData(hasFilter)) {
		for (let index = 0; index < hasFilter; index++) {
			filters += '<select class="form-control form-control-sm float-end me-2 skeleton" style="width: 12%!important;"></select>';
		}
	}

	let buttonShow = buttonRefresh ? '<div class="col-xl-12 mb-4">\
										<button type="button" class="btn btn-default btn-sm float-end skeleton">  &nbsp;&nbsp;&nbsp; </button>\
										<button type="button" class="btn btn-default btn-sm float-end me-2 skeleton">\
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
										</button>\
										' + filters + '\
										</div><br><br>' : '';

	return '<div class="row mt-2">\
				<div class="col-md-12 col-lg-12">\
					<div class="card" id="bodyDiv">\
						<div class="card-body">\
							' + buttonShow + '\
							<div class="col-xl-12 mt-2">\
								<table class="table table-bordered">\
									<tbody>' + body + '</tbody>\
								</table>\
							</div>\
						</div>\
					</div>\
				</div>\
			</div>';
}

const getImageDefault = (imageName, path = 'public/upload/default/') => {
	return urls(path + imageName);
}


const generateClientDt = async (id, url = null, dataObj = null, filterColumn = [], nodatadiv = 'nodatadiv', screenLoadID = 'nodata') => {

	const tableID = $('#' + id);
	var table = tableID.DataTable().clear().destroy();

	loading('#' + screenLoadID, true);

	const res = await callApi('get', url, dataObj);

	if (isSuccess(res)) {
		if (hasData(res.data)) {
			table = tableID.DataTable({
				"data": res.data,
				"deferRender": true,
				"processing": true,
				"serverSide": false,
				'paging': true,
				'ordering': true,
				'info': true,
				'responsive': true,
				'iDisplayLength': 10,
				'bLengthChange': true,
				'searching': true,
				'autoWidth': false,
				'language': {
					"searchPlaceholder": 'Search...',
					"sSearch": '',
					// "lengthMenu": '_MENU_ item / page',
					// "paginate": {
					// 	"first": "First",
					// 	"last": "The End",
					// 	"previous": "Previous",
					// 	"next": "Next"
					// },
					// "info": "Showing _START_ to _END_ of _TOTAL_ items",
					// "emptyTable": "No data is available in the table",
					// "info": "Showing _START_ to  _END_ of  _TOTAL_ items",
					// "infoEmpty": "Showing 0 to 0 of 0 items",
					// "infoFiltered": "(filtered from _MAX_ number of items)",
					// "zeroRecords": "No matching records",
					// "processing": "<span class='text-danger font-weight-bold font-italic'> Processing ... Please wait a moment..",
					// "loadingRecords": "Loading...",
					// "infoPostFix": "",
					// "thousands": ",",
				},
				'columnDefs': filterColumn,
			});
			$('#' + nodatadiv).hide();
			$('#' + id + 'Div').show();
		} else {
			$('#' + nodatadiv).empty(); // reset
			$('#' + nodatadiv).html(nodata());
			$('#' + nodatadiv).show();
			$('#' + id + 'Div').hide();
		}
	}

	loading('#' + screenLoadID, false);

	return table;
}

const generateServerDt = (id, url = null, nodatadiv = 'nodatadiv', dataObj = null, filterColumn = []) => {

	const tableID = $('#' + id);
	tableID.DataTable().clear().destroy();

	let dataSent = null;

	if (dataObj != null) {
		dataObj[csrf_token_name] = Cookies.get(csrf_cookie_name) // csrf token
		// dataSent = new URLSearchParams(dataObj);
		dataSent = dataObj;
	}

	let ajaxConfig = {
		type: 'POST',
		url: $('meta[name="base_url"]').attr('content') + url,
		dataType: "JSON",
		data: dataSent,
		headers: {
			"Authorization": "Bearer " + Cookies.get(csrf_cookie_name),
			'X-Requested-With': 'XMLHttpRequest',
			'content-type': 'application/x-www-form-urlencoded',
			"X-CSRF-TOKEN": Cookies.get(csrf_cookie_name),
		},
		"error": function (xhr, error, exception) {
			if (exception) {
				if (isError(xhr.status))
					noti(xhr.status, exception);
			}
		}
	};

	if (dataSent == null) {
		delete ajaxConfig['data'];
	}

	let tableConfig = {
		// "pagingType": "full_numbers",
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"iDisplayLength": 10,
		"bLengthChange": true,
		"searching": true,
		"ajax": ajaxConfig,
		"language": {
			"searchPlaceholder": 'Search...',
			"sSearch": '',
			// "lengthMenu": '_MENU_ item / page',
			// "paginate": {
			// 	"first": "First",
			// 	"last": "The End",
			// 	"previous": "Previous",
			// 	"next": "Next"
			// },
			// "info": "Showing _START_ to _END_ of _TOTAL_ items",
			// "emptyTable": "No data is available in the table",
			// "info": "Showing _START_ to _END_ of _TOTAL_ items",
			// "infoEmpty": "Showing 0 to 0 of 0 items",
			// "infoFiltered": "(filtered from _MAX_ number of items)",
			// "zeroRecords": "No matching records",
			// "processing": "<span class='text-danger font-weight-bold font-italic'> Processing ... Please wait a moment.. ",
			// "loadingRecords": "Loading...",
			// "infoPostFix": "",
			// "thousands": ",",
		},
		"columnDefs": filterColumn,
		initComplete: function () {

			var totalData = this.api().data().length;

			if (totalData > 0) {
				$('#' + nodatadiv).hide();
				$('#' + id + 'Div').show();
			} else {
				tableID.DataTable().clear().destroy();
				$('#' + id + 'Div').hide();
				$('#' + nodatadiv).show();
			}

		}
	};

	return tableID.DataTable(tableConfig);
}
