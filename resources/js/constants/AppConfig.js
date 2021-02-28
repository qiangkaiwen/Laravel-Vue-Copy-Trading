/**
 * App Config File
 */
export default {
	appLogo: '/static/img/White on Transparent.png',                                   // App Logo,
	darkLogo: '/static/img/Black on Transparent.png',							    // dark logo
	appLogo2: '/static/img/White on Transparent.png',                                    // App Logo 2 For Login & Signup Page
	brand: 'Vuely',                                        			        // Brand Name
	copyrightText: 'Vuely © 2021 All Rights Reserved.',                     // Copyright Text
	enableUserTour: process.env.NODE_ENV === 'production' ? true : false,   // Enable User Tour
	weatherApiId: 'b1b15e88fa797225412429c1c50c122a1',						// weather API Id
	weatherApiKey: '69b72ed255ce5efad910bd946685883a'						// weather APi key
}
