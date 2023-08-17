// CODEIGNITER 3 : CSRF must be same as in ENV Files.
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

// DEBUG HELPER

/**
 * Function: log
 * Description: This function takes in multiple arguments and logs each argument to the console.
 * It iterates through the provided arguments and uses the console.log() function to display each argument's value in the console.
 *
 * @param {...any} args - The arguments to be logged to the console.
 * 
 * @example
 * log("Hello", 42, [1, 2, 3]);
 */
const log = (...args) => {
	args.forEach((param) => {
		console.log(param);
	});
}

/**
 * Function: dd
 * Description: This function is similar to the 'log' function, but it additionally throws an error after logging the provided arguments.
 * It is typically used for debugging purposes to terminate program execution and print diagnostic information at a specific point in the code.
 *
 * @param {...any} args - The arguments to be logged to the console before terminating the execution.
 * @throws {Error} - Always throws an error with the message "Execution terminated by dd()".
 * 
 * @example
 * dd("Error occurred", { code: 500 });
 */
const dd = (...args) => {
	args.forEach((param) => {
		console.log(param);
	});
	throw new Error("Execution terminated by dd()");
}

/**
 * Function: jsonHtmlHighlight
 * Description: Converts a JSON object or string into HTML-highlighted syntax.
 *
 * @param {string | object} json - The JSON object or string to be highlighted.
 * 
 * @example
 * const highlightedJson = jsonHtmlHighlight({"key": "value"}); // highlightedJson is an HTML-formatted string with syntax highlighting for JSON.
 */
const jsonHtmlHighlight = (json) => {
	try {
		// Convert to string if not already a string
		if (typeof json !== 'string') {
			json = JSON.stringify(json, undefined, 2);
		}

		// Replace special characters for HTML display
		json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

		// Apply syntax highlighting using regex
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
	} catch (error) {
		console.error(`An error occurred in jsonHtmlHighlight(): ${error.message}`);
		return ''; // Return empty string in case of an error
	}
}

// DATA HELPER

/**
 * Function: isset
 * Description: Checks if a variable is defined and not null.
 *
 * @param {*} variable - The variable to be checked.
 * @returns {boolean} - True if the variable is defined and not null, false otherwise.
 * 
 * @example
 * const result = isset(myVar);
 * if (result) {
 *   // myVar is defined and not null
 * } else {
 *   // myVar is undefined or null
 * }
 */
const isset = (variable) => {
	return typeof variable != 'undefined' && variable != null;
};

/**
 * Function: trimData
 * Description: Trims leading and trailing whitespace from a given string if it's defined, otherwise returns original text.
 *
 * @param {*} text - The text to be potentially trimmed.
 * @returns {string | *} - Returns the trimmed string or the original value if input is not a string.
 * 
 * @example
 * const trimmedText = trimData("   Some text   "); // trimmedText now contains "Some text"
 * const nullResult = trimData(null); // nullResult is null
 * const numberResult = trimData(6); // numberResult return as is
 */
const trimData = (text) => {
	return typeof text === 'string' ? text.trim() : text;
};

/**
 * Function: hasData
 * Description: Check if data exists and optionally if a nested key exists within the data.
 *
 * @param {any} data - The data to be checked.
 * @param {string} arrKey - A dot-separated string representing the nested keys to check within the data.
 * @param {boolean} returnData - If true, return the data instead of a boolean.
 * @param {any} defaultValue - The value to return if the data or nested key is not found.
 * @returns {boolean | any} - Returns a boolean indicating data existence or the actual data based on `returnData` parameter.
 */
const hasData = (data = null, arrKey = null, returnData = false, defaultValue = null) => {
	if (!data || data === '' || data === 'null') {
		return returnData ? defaultValue : false;
	}

	if (!arrKey) {
		return true;
	}

	const keys = arrKey.split('.');
	let currentData = data;

	for (const key of keys) {
		if (!(key in currentData)) {
			return returnData ? defaultValue : false;
		}
		currentData = currentData[key];
	}

	return !returnData ? true : (!currentData || currentData === 'null' ? defaultValue : currentData);
};

/**
 * Function: ucfirst
 * Description: Converts the first character of a string to uppercase.
 *
 * @param {string} string - The input string.
 * @returns {string} - The input string with the first character capitalized.
 * 
 * @example
 * const result = ucfirst("hello"); // Result is "Hello"
 */
const ucfirst = (string) => {
	try {
		if (typeof string !== 'string') {
			throw new Error(`An error occurred in ucfirst(): Input must be a string`);
		}
		return string.charAt(0).toUpperCase() + string.slice(1);
	} catch (error) {
		console.error(`An error occurred in ucfirst(): ${error.message}`);
		return string;
	}
}

/**
 * Function: ucwords
 * Description: Capitalizes the first character of each word in a string.
 *
 * @param {string} str - The input string.
 * @returns {string} - The input string with the first character of each word capitalized.
 * 
 * @example
 * const result = ucwords("hello world"); // Result is "Hello World"
 */
const ucwords = (str) => {
	try {
		if (typeof str !== 'string') {
			throw new Error(`An error occurred in ucwords(): Input must be a string`);
		}
		return str.toLowerCase().split(' ').map(function (word) {
			return word.replace(word[0], word[0].toUpperCase());
		}).join(' ');
	} catch (error) {
		console.error(`An error occurred in ucwords(): ${error.message}`);
		return str;
	}
}

/**
 * Function: strtoupper
 * Description: Converts the value of string to uppercase
 *
 * @param {string} str - The input string.
 * @returns {string} - The input string with the uppercase.
 * 
 * @example
 * const result = strtoupper('hello'); // Result is "HELLO"
 */
const strtoupper = (str) => {
	try {
		if (typeof str !== 'string') {
			throw new Error(`An error occurred in strtoupper(): Input must be a string`);
		}
		return str.toUpperCase();
	} catch (error) {
		console.error(`An error occurred in strtoupper(): ${error.message}`);
		return str;
	}
}

/**
 * Function: strtolower
 * Description: Converts a string to lowercase.
 *
 * @param {string} str - The input string.
 * @return {string} - The input string converted to lowercase.
 *
 * @example
 * const result = strtolower("Hello World"); // result is "hello world"
 */
const strtolower = (str) => {
	try {
		if (typeof str !== 'string') {
			throw new Error(`An error occurred in strtolower(): Input must be a string`);
		}
		return str.toLowerCase();
	} catch (error) {
		console.error(`An error occurred in strtolower(): ${error.message}`);
		return str;
	}
}

/**
 * Function: str_replace
 * Description: Replaces all occurrences of a substring in a string with another substring.
 *
 * @param {string} find - The substring to be replaced.
 * @param {string} replace - The replacement substring.
 * @param {string} string - The input string.
 * @return {string} - The input string with all occurrences of the search substring replaced by the replace substring.
 *
 * @example
 * const result = str_replace("world", "universe", "Hello world"); // result is "Hello universe"
 */
const str_replace = (find, replace, string) => {

	try {
		if (typeof string !== 'string') {
			throw new Error(`An error occurred in str_replace(): String text must be a string`);
		}

		if (typeof find !== 'string') {
			throw new Error(`An error occurred in str_replace(): Find must be a string`);
		}

		if (typeof replace !== 'string') {
			throw new Error(`An error occurred in str_replace(): Replace must be a string`);
		}

		return string.split(find).join(replace);
	} catch (error) {
		console.error(`An error occurred in str_replace(): ${error.message}`);
		return str;
	}
}

// ARRAY HELPER

/**
 * Function: in_array
 * Description: Checks if a given value exists in the provided array.
 *
 * @param {*} needle - The value to search for in the array.
 * @param {Array} data - The array to search within.
 * @returns {boolean} - True if the value exists in the array, false otherwise.
 * 
 * @example
 * const result = in_array(42, [1, 42, 3]); // result is true
 */
const in_array = (needle, data) => {
	if (!Array.isArray(data)) {
		throw new Error("An error occurred in in_array(): data should be an array");
	}

	try {
		return data.includes(needle);
	} catch (error) {
		throw new Error(`An error occurred in in_array(): ${error.message}`);
	}
}

/**
 * Function: array_push
 * Description: Adds one or more elements to the end of an array and returns the new length of the array.
 *
 * @param {Array} data - The array to which elements will be added.
 * @param {...*} elements - Elements to be added to the array.
 * @returns {number} - The new length of the array.
 * 
 * @example
 * const myArray = [1, 2];
 * const newLength = array_push(myArray, 3, 4); // myArray is now [1, 2, 3, 4], newLength is 4
 */
const array_push = (data, ...elements) => {
	if (!Array.isArray(data)) {
		throw new Error("An error occurred in array_push(): data should be an array");
	}

	try {
		return data.push(...elements);
	} catch (error) {
		throw new Error(`An error occurred in array_push(): ${error.message}`);
	}
}

/**
 * Function: array_merge
 * Description: Merges multiple arrays into a single array.
 *
 * @param {...Array} arrays - Arrays to be merged.
 * @returns {Array} - The merged array.
 * 
 * @example
 * const mergedArray = array_merge([1, 2], [3, 4], [5, 6]); // mergedArray is [1, 2, 3, 4, 5, 6]
 */
const array_merge = (...arrays) => {
	for (const array of arrays) {
		if (!Array.isArray(array)) {
			throw new Error("All arguments should be arrays");
		}
	}

	try {
		return [].concat(...arrays);
	} catch (error) {
		throw new Error(`An error occurred in array_merge(): ${error.message}`);
	}
}

/**
 * Function: implode
 * Description: Joins elements of an array into a string using a specified separator.
 *
 * @param {string} separator - The separator string used between array elements.
 * @param {Array} data - The array whose elements will be joined.
 * @returns {string} - The joined string.
 * 
 * @example
 * const result = implode(', ', ['apple', 'banana', 'orange']); // result is "apple, banana, orange"
 */
const implode = (separator = ',', data) => {
	if (data !== null && !Array.isArray(data)) {
		throw new Error(`An error occurred in implode(): data should be an array`);
	}

	try {
		return data.join(separator);
	} catch (error) {
		throw new Error(`An error occurred in implode(): ${error.message}`);
	}
}

/**
 * Function: explode
 * Description: Splits a string into an array of substrings based on a specified delimiter.
 *
 * @param {string} delimiter - The delimiter to use for splitting the string.
 * @param {string} data - The string to be split.
 * @returns {Array} - An array of substrings.
 * 
 * @example
 * const result = explode(' ', 'Hello world'); // result is ["Hello", "world"]
 */
const explode = (delimiter = ',', data) => {
	if (typeof data !== 'string') {
		throw new Error("An error occurred in explode(): data should be a string");
	}

	try {
		return data.split(delimiter);
	} catch (error) {
		throw new Error(`An error occurred in explode(): ${error.message}`);
	}
}

/**
 * Function: array_key_exists
 * Description: Checks if a specified key exists in an object.
 *
 * @param {*} arrKey - The key to check for existence in the object.
 * @param {Object} data - The object to check for the key's existence.
 * @returns {boolean} - True if the key exists in the object, false otherwise.
 * @throws {Error} - Throws an error if data is not an object.
 * 
 * @example
 * const obj = { name: 'John', age: 30 };
 * const result = array_key_exists('name', obj);
 * // result is true
 */
const array_key_exists = (arrKey, data) => {
	if (typeof data !== 'object' || data === null) {
		throw new Error("An error occurred in array_key_exists(): data should be an object");
	}

	try {
		if (data.hasOwnProperty(arrKey)) {
			return true;
		}

		return false;
	} catch (error) {
		throw new Error(`An error occurred in array_key_exists(): ${error.message}`);
	}
}

/**
 * Function: remove_item_array
 * Description: Removes a specified item from an array if it exists.
 *
 * @param {Array} data - The array from which the item will be removed.
 * @param {*} item - The item to be removed from the array.
 * @returns {*} - The removed item, or undefined if the item doesn't exist in the array.
 * 
 * @example
 * const myArray = [1, 2, 3, 4];
 * const removedItem = remove(myArray, 2); // myArray is now [1, 3, 4], removedItem is 2
 */
const remove_item_array = (data, item) => {
	if (!Array.isArray(data)) {
		throw new Error("An error occurred in remove_item_array(): data should be an array");
	}

	const index = data.indexOf(item);
	if (index > -1) {
		try {
			return data.splice(index, 1)[0];
		} catch (error) {
			throw new Error(`An error occurred in remove_item_array(): ${error.message}`);
		}
	}

	return undefined;
};

// DATE & TIME HELPER

/**
 * Function: getCurrentTime
 * Description: Gets the current time in the specified format.
 *
 * @param {boolean} use12HourFormat - Optional. If true, the time will be in 12-hour format (AM/PM).
 *                                    If false or not provided, the time will be in 24-hour format.
 * @param {boolean} hideSeconds - Optional. If true, the seconds portion will be hidden.
 * @returns {string} The current time in the specified format.
 *
 * @example
 * const result24 = getCurrentTime();                    // result is like "14:30:45"
 * const result12 = getCurrentTime(true);                // result is like "02:30:45 PM"
 * const result12NoSeconds = getCurrentTime(true, true); // result is like "02:30 PM"
 */
const getCurrentTime = (use12HourFormat = false, hideSeconds = false) => {
	try {
		const today = new Date();
		let hh = today.getHours();
		const mm = today.getMinutes().toString().padStart(2, '0');
		let ss = '';

		if (!hideSeconds) {
			ss = `:${today.getSeconds().toString().padStart(2, '0')}`;
		}

		let timeFormat = "24-hour";

		if (use12HourFormat) {
			timeFormat = "12-hour";
			const period = hh >= 12 ? "PM" : "AM";
			hh = hh % 12 || 12; // Convert 0 to 12 for 12-hour format
			return `${hh}:${mm}${ss} ${period}`;
		}

		hh = hh.toString().padStart(2, '0');
		return `${hh}:${mm}${ss}`;
	} catch (error) {
		console.error(`An error occurred in getCurrentTime(): ${error.message}`);
		return "00:00:00";
	}
};

/**
 * Function: getCurrentDate
 * Description: Gets the current date in YYYY-MM-DD format.
 *
 * @returns {string} - The current date.
 * 
 * @example
 * const result = getCurrentDate(); // result is like "2023-08-17"
 */
const getCurrentDate = () => {
	try {
		const today = new Date();
		const dd = today.getDate().toString().padStart(2, '0');
		const mm = (today.getMonth() + 1).toString().padStart(2, '0'); // January is 0 so need to add 1
		const yyyy = today.getFullYear();
		return `${yyyy}-${mm}-${dd}`;
	} catch (error) {
		console.error(`An error occurred in getCurrentDate(): ${error.message}`);
		return "1970-01-01";
	}
}

/**
 * Function: getCurrentTimestamp
 * Description: Gets the current timestamp in the format "YYYY-MM-DD HH:MM:SS".
 *
 * @returns {string} The current timestamp in the format "YYYY-MM-DD HH:MM:SS".
 *
 * @example
 * const timestamp = getCurrentTimestamp(); // Returns something like "2023-08-17 14:30:45"
 */
const getCurrentTimestamp = () => {
	try {
		const now = new Date();
		const yyyy = now.getFullYear();
		const mm = (now.getMonth() + 1).toString().padStart(2, '0'); // January is 0 so need to add 1
		const dd = now.getDate().toString().padStart(2, '0');
		const hh = now.getHours().toString().padStart(2, '0');
		const min = now.getMinutes().toString().padStart(2, '0');
		const ss = now.getSeconds().toString().padStart(2, '0');

		return `${yyyy}-${mm}-${dd} ${hh}:${min}:${ss}`;
	} catch (error) {
		console.error(`An error occurred in getCurrentTimestamp(): ${error.message}`);
		return "1970-01-01 00:00:00"; // Return default value in case of error
	}
};

/**
 * Function: getClock
 * Description: Returns a formatted current time along with the day name and date in the specified language.
 *
 * @param {string} format - The time format, either '12' (12-hour) or '24' (24-hour). Default is '24'.
 * @param {string} lang - The language code, either 'en' (English), 'my' (Malay), or 'id' (Indonesian). Default is 'en'.
 * @param {boolean} showSeconds - Whether to include seconds in the formatted time string. Default is true.
 * 
 * @return {string} - The formatted time string.
 * 
 * @example
 * // const time = getClock('24', 'en', true); // Returns a 24-hour time string with seconds in English.
 */
const getClock = (format = '24', lang = 'en', showSeconds = true) => {
	try {
		// Define day names in English, Malay, and Indonesian
		const dayNames = {
			en: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
			my: ['Ahad', 'Isnin', 'Selasa', 'Rabu', 'Khamis', 'Jumaat', 'Sabtu'],
			id: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
		};

		// Validate the format parameter
		if (format !== '12' && format !== '24') {
			throw new Error("An error occurred in getClock(): Invalid format parameter. Use '12' or '24'.");
		}

		// Validate the lang parameter
		if (!dayNames[lang]) {
			throw new Error("An error occurred in getClock(): Invalid lang parameter. Use 'en', 'my', or 'id'.");
		}

		// Get the current date and time
		const currentTime = new Date();
		const currentDayIndex = currentTime.getDay(); // Get the day index (0-6)

		// Get the appropriate day name based on the current day index and language
		const dayName = dayNames[lang][currentDayIndex];

		// Get hours, minutes, and seconds
		let hours = currentTime.getHours();
		const minutes = currentTime.getMinutes();
		const seconds = currentTime.getSeconds();

		// Convert to 12-hour format and determine AM/PM if format is '12'
		let ampm = '';
		if (format === '12') {
			ampm = hours >= 12 ? 'PM' : 'AM';
			hours = hours % 12 || 12; // Convert 0 to 12
		}

		// Add leading zeros to hours, minutes, and seconds if necessary
		hours = hours < 10 ? '0' + hours : hours;
		const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
		const formattedSeconds = seconds < 10 ? '0' + seconds : seconds;

		// Build the time string
		let createTime = `${hours}:${formattedMinutes}`;

		if (showSeconds) {
			createTime += `:${formattedSeconds}`;
		}

		// Build the formatted time string
		let displayTime = format === '24'
			? createTime
			: `${createTime} ${ampm}`;

		return `${dayName}, ${displayTime}`;
	} catch (error) {
		console.error(`An error occurred in getClock(): ${error.message}`);
		return ''; // Return an empty string in case of an error
	}
};

/**
 * Function: isWeekend
 * Description: Checks if the given date falls on a weekend (Saturday or Sunday).
 *
 * @param {Date} date - The date to check. Defaults to the current date if not provided.
 * @returns {boolean} - Returns true if the date is a weekend, otherwise false.
 * 
 * @example
 * const result = isWeekend(new Date(2023, 8, 17)); // result is false
 */
const isWeekend = (date = new Date()) => {
	try {
		const day = date.getDay();
		return day === 0 || day === 6;
	} catch (error) {
		console.error(`An error occurred in isWeekend(): ${error.message}`);
		return false;
	}
}

/**
 * Function: isWeekday
 * Description: Checks if the given date is a weekday (Monday to Friday).
 *
 * @param {Date} date - The date to be checked. Default is the current date.
 * @returns {boolean} True if the date is a weekday, otherwise false.
 *
 * @example
 * const result = isWeekday(new Date('2023-08-19')); // Returns true if '2023-08-19' is a weekday.
 */
const isWeekday = (date = new Date()) => {
	try {
		const day = date.getDay(); // Sunday: 0, Monday: 1, ..., Saturday: 6
		return day >= 1 && day <= 5; // Weekdays are from Monday (1) to Friday (5)
	} catch (error) {
		console.error(`An error occurred in isWeekday(): ${error.message}`);
		return false;
	}
};

// INPUT VALIDATOR HELPER

/**
 * Function: isNumberKey
 * Description: Checks if the pressed key is a number key (0-9).
 *
 * @param {Event} evt - The event object representing the key press.
 * 
 * @example
 * // Returns true if the pressed key is a number key (0-9), otherwise false.
 */
const isNumberKey = (evt) => {
	try {
		const charCode = (evt.which) ? evt.which : evt.keyCode;
		return charCode > 31 && charCode < 48 || charCode > 57;
	} catch (error) {
		throw new Error(`An error occurred in isNumberKey(): ${error.message}`);
	}
};

/**
 * Function: isNumeric
 * Description: Validates whether the pressed key is a numeric digit or a decimal point.
 *
 * @param {Event} evt - The input event object.
 * 
 * @example
 * // Prevents non-numeric characters from being entered in an input field.
 * // Usage example: <input type="text" onkeypress="isNumeric(event);">
 */
const isNumeric = (evt) => {
	try {
		const theEvent = evt || window.event;
		const key = String.fromCharCode(theEvent.keyCode || theEvent.which);
		const regex = /[0-9]|\./;

		if (!regex.test(key)) {
			if (theEvent.preventDefault) theEvent.preventDefault();
			return false;
		}
	} catch (error) {
		console.error(`An error occurred in isNumeric(): ${error.message}`);
	}
};

/**
 * Function: maxLengthCheck
 * Description: Truncates the input value of an object if its length exceeds the specified maximum length.
 *
 * @param {Object} object - The object containing 'value' and 'maxLength' properties.
 * 
 * @example
 * const inputObject = { value: "someLongTextHere", maxLength: 10 };
 * maxLengthCheck(inputObject);
 * // After the function call, inputObject.value will be "someLongTe"
 */
const maxLengthCheck = (object) => {
	try {
		if (object.value.length > object.maxLength) {
			object.value = object.value.slice(0, object.maxLength);
		}
	} catch (error) {
		console.error(`An error occurred in maxLengthCheck(): ${error.message}`);
	}
}

// CUSTOM - LOADER & BUTTON HELPER

/**
 * Function: loading
 * Description: Toggle the display of a loading overlay using jQuery blockUI plugin.
 *
 * @param {string} id - The ID of the element to show the loading overlay on.
 * @param {boolean} display - Whether to display the loading overlay (true) or hide it (false).
 * 
 * @example
 * // Display loading overlay
 * loading('#loading-container', true);
 * 
 * // Hide loading overlay
 * loading('#loading-container', false);
 */
const loading = (id = null, display = false) => {
	try {
		if (!id) {
			throw new Error(`An error occurred in loading(): ID parameter is required.`);
		}

		if (display) {
			$(id).block({
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
			}, 100);
		}
	} catch (error) {
		throw new Error(`An error occurred in loading(): ${error.message}`);
	}
}

/**
 * Function: loadingBtn
 * Description: Toggle button text and disabled state to show loading or normal state.
 *
 * @param {string} id - The ID of the button element.
 * @param {boolean} display - Whether to display the loading state.
 * @param {string} text - The text to set for the button when not in loading state.
 * 
 * @example
 * // To show loading state:
 * loadingBtn("myButtonId", true);
 * 
 * // To revert to normal state:
 * loadingBtn("myButtonId", false, "Save");
 */
const loadingBtn = (id, display = false, text = "<i class='ti ti-device-flopy ti-xs mb-1'></i> Save") => {
	const buttonElement = $("#" + id);

	if (display) {
		buttonElement.html('Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>');
		buttonElement.prop('disabled', true); // $("#" + id).attr('disabled', true);
	} else {
		buttonElement.html(text);
		buttonElement.prop('disabled', false); // $("#" + id).attr('disabled', false);
	}
}

/**
 * Function: disableBtn
 * Description: Disables or enables a button element based on the provided ID, and optionally updates its display and text.
 *
 * @param {string} id - The ID of the button element to disable/enable.
 * @param {boolean} display - Determines whether the button should be displayed (true) or hidden (false). Default is true.
 * @param {string|null} text - Optional text to update the button content while disabling it. Default is null.
 * 
* @example
 * // Disabling a button with ID "myButton":
 * disableBtn("myButton", true, "Processing...");
 * 
 * // Enabling a button with ID "myButton":
 * disableBtn("myButton", false);
 */
const disableBtn = (id, display = true, text = null) => {
	const button = $("#" + id);
	button.prop("disabled", display);

	if (text !== null) {
		button.html(text);
	}
}

// UPLOAD HELPER

/**
 * Function: sizeToText
 * Description: Converts a file size in bytes to a human-readable format with appropriate units (B, KB, MB, GB, TB).
 *
 * @param {number} size - The file size in bytes.
 * @param {number} decimal - (Optional) The number of decimal places to round the result to. Default is 2.
 * 
 * @example
 * const result = sizeToText(123456789); // result is "117.74 MB"
 */
const sizeToText = (size, decimal = 2) => {
	try {
		if (typeof size !== 'number') {
			throw new Error('An error occurred in sizeToText(): Invalid input - size must be a number');
		}

		if (typeof decimal !== 'number') {
			throw new Error('Decimal must be a number.');
		}

		if (decimal < 0) {
			throw new Error('Decimal cannot be negative.');
		}

		const sizeContext = ["B", "KB", "MB", "GB", "TB"];
		let atCont = 0;

		while (size >= 1024 && atCont < sizeContext.length - 1) {
			size /= 1024;
			atCont++;
		}

		return `${(size).toFixed(decimal)} ${sizeContext[atCont]}`;

	} catch (error) {
		throw new Error(`An error occurred in sizeToText(): ${error.message}`);
	}
}

// GENERAL HELPER

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

					const disSize = sizeToText(progressEvent.total);
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

					$('#componentProgressBarStatusCanthink').html(`${sizeToText(progressEvent.loaded)} of ${disSize} | ${percentCompleted}% uploading <br> estimated time remaining: ${time}`);

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

const skeletonTableOnly = (totalData = 5) => {

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

	return '<div class="col-xl-12 mt-2">\
				<button type="button" class="btn btn-default btn-sm skeleton">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </button>\
				<button type="button" class="btn btn-default btn-sm float-end skeleton mb-3">\
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
				</button>\
				<table class="table">\
					<tbody>' + body + '</tbody>\
				</table>\
				<button type="button" class="btn btn-default btn-sm float-end skeleton">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>\
				<button type="button" class="btn btn-default btn-sm me-1 float-end skeleton">&nbsp;&nbsp;</button>\
				<button type="button" class="btn btn-default btn-sm me-1 float-end skeleton">&nbsp;&nbsp;</button>\
				<button type="button" class="btn btn-default btn-sm me-1 float-end skeleton">&nbsp;&nbsp;</button>\
				<button type="button" class="btn btn-default btn-sm me-1 float-end skeleton">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>\
			</div>';
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

// DATATABLE HELPER

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

const generateServerDt = (id, url = null, nodatadiv = 'nodatadiv', dataObj = null, filterColumn = [], screenLoadID = null) => {

	const tableID = $('#' + id);
	tableID.DataTable().clear().destroy();

	let dataSent = null;

	if (dataObj != null) {
		dataObj[csrf_token_name] = Cookies.get(csrf_cookie_name) // csrf token
		// dataSent = new URLSearchParams(dataObj);
		dataSent = dataObj;
	}

	if (screenLoadID != null) {
		// loading('#' + screenLoadID, true);
		$('#' + id + 'Div').hide();
		$('#' + nodatadiv).empty();
		$('#' + nodatadiv).hide();
		$('#' + screenLoadID).html(skeletonTableOnly(5));
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

			if (screenLoadID != null) {
				$('#' + screenLoadID).empty();
			}

			if (totalData > 0) {
				$('#' + nodatadiv).hide();
				$('#' + id + 'Div').show();
			} else {
				tableID.DataTable().clear().destroy();
				$('#' + id + 'Div').hide();
				$('#' + nodatadiv).html(nodata());
				$('#' + nodatadiv).show();
			}

		}
	};

	return tableID.DataTable(tableConfig);
}

// IMPORT EXCEL & PRINT HELPER

const printHelper = async (method = 'get', url, filter = null, config = null) => {

	let btnID = hasData(config, 'id', true, 'printBtn');
	let btnText = hasData(config, 'text', true, '<i class="bx bx-printer"></i> Print');
	let textHeader = hasData(config, 'header', true, 'LIST');

	loadingBtn(btnID, true);

	const res = await callApi(method, url, filter);

	if (isSuccess(res)) {

		if (isSuccess(res.data.resCode)) {
			const divToPrint = document.createElement('div');
			divToPrint.setAttribute('id', 'generatePDF');
			divToPrint.innerHTML = res.data.result

			document.body.appendChild(divToPrint);
			printDiv('generatePDF', btnID, $('#' + btnID).html(), textHeader);
			document.body.removeChild(divToPrint);
		} else {
			noti(res.data.resCode, res.data.message);
			console.log(res.data.resCode, res.data.message);
		}

		setTimeout(function () {
			loadingBtn(btnID, false, btnText);
		}, 450);
	}
}

// EXPORT LIST TO EXCEL
const exportExcelHelper = async (method = 'get', url, filter = null, config = null) => {

	let btnID = hasData(config, 'id', true, 'exportBtn');
	let btnText = hasData(config, 'text', true, '<i class="bx bx-spreadsheet"></i> Export as Excel');

	loadingBtn(btnID, true);

	const res = await callApi(method, url, filter);

	if (isSuccess(res)) {
		noti(res.data.resCode, res.data.message);

		// Create a link to download the Excel file
		const link = document.createElement('a');
		link.href = res.data.path;
		link.download = res.data.filename;
		document.body.appendChild(link);

		// Click the link to start the download
		link.click();

		// Remove the link from the DOM
		document.body.removeChild(link);
	}

	setTimeout(function () {
		loadingBtn(btnID, false, btnText);
	}, 450);
}