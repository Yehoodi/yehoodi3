<?php

	// Set up the route for the discussion pages
	$discussionRoute = new Zend_Controller_Router_Route('discussion/:action/:category/:range/:order/:page/*',
												array('controller'	=> 'discussion',
													  'action'		=> 'all',
													  'category'	=> 'all',
													  'range'       => '30days',
													  'order'		=> 'date',
													  'page'		=> 1
													  )
											 );
	$frontController->getRouter()->addRoute('discussion',$discussionRoute);
													  
	// Set up the route for the comment pages
	$commentRoute = new Zend_Controller_Router_Route('comment/:id/:url/:page/*',
												array('controller'	=> 'comment',
													  'action'		=> 'index',
													  'id'			=> 1,
													  'url'			=> '',
													  'page'		=> 1
													  )
											 );
	$frontController->getRouter()->addRoute('comment',$commentRoute);

	// Set up the route for the profile pages
	$profileRoute = new Zend_Controller_Router_Route('profile/:username/*',
												array('controller'	=> 'profile',
												      'username'	=> 'yehoodi',
													  'action'		=> 'index'
													  )
											 );
	$frontController->getRouter()->addRoute('profile',$profileRoute);

	// Set up the route for the submit page
	$submitRoute = new Zend_Controller_Router_Route('submit/:id/*',
												array('controller'	=> 'submit',
													  'action'		=> 'index',
													  'id'			=> '0'
													  )
											 );
	$frontController->getRouter()->addRoute('submit',$submitRoute);

	// Set up the route for the search pages
	$searchRoute = new Zend_Controller_Router_Route('search/*',
												array('controller'	=> 'search',
													  'action'		=> 'index'
													  )
											 );
	$frontController->getRouter()->addRoute('search',$searchRoute);
													  
	// MAIL MAIN
	$mailRoute = new Zend_Controller_Router_Route('mail/:box/:page/*',
												array('controller'	=> 'mail',
													  'action'		=> 'index',
													  'box'			=> 'inbox',
													  'page'		=> 1
													  )
											 );
	$frontController->getRouter()->addRoute('mail',$mailRoute);

	// MAIL MESSAGES
	$mailMessageRoute = new Zend_Controller_Router_Route('mail/message/:id/:page/*',
												array('controller'	=> 'mail',
													  'action'		=> 'message',
													  'id'			=> 0,
													  'page'		=> 1
													  )
											 );
	$frontController->getRouter()->addRoute('mailMessage',$mailMessageRoute);

	// Set up the route for the account pages
	$accountRoute = new Zend_Controller_Router_Route('account/:action/:view/*',
												array('controller'	=> 'account',
													  'action'		=> 'index',
													  'view'		=> ''
													  )
											 );
	$frontController->getRouter()->addRoute('account',$accountRoute);

	// Set up the route for the cron pages
	$cronRoute = new Zend_Controller_Router_Route('cron/:resource/*',
												array('controller'	=> 'cron',
													  'action'		=> 'index',
													  'resource'	=> ''
													  )
											 );
	$frontController->getRouter()->addRoute('cron',$cronRoute);

	// Set up the route for the help pages
	$helpRoute = new Zend_Controller_Router_Route('help/:page/*',
												array('controller'	=> 'help',
													  'action'		=> 'index',
													  'page'       	=> ''
													  )
											 );
	$frontController->getRouter()->addRoute('help',$helpRoute);

	// Set up the route for the calendar page
	$calendarRoute = new Zend_Controller_Router_Route('calendar/:caltype/:category/*',
												array('controller'	=> 'calendar',
													  'caltype'		=> 'month',
													  'action'		=> 'index',
													  'category'	=> 'all'
													  )
											 );
	$frontController->getRouter()->addRoute('calendar',$calendarRoute);

	// Set up the route for the ilhc page
	$ilhcRoute = new Zend_Controller_Router_Route('ilhc/*',
												array('controller'	=> 'ilhc',
													  'action'		=> 'index',
													  'page'       	=> ''
													  )
											 );
	$frontController->getRouter()->addRoute('ilhc',$ilhcRoute);	// Set up the route for the ilhc page

	// Set up the route for the Frankie100 page
	$frankie100Route = new Zend_Controller_Router_Route('frankie100/*',
												array('controller'	=> 'frankie100',
													  'action'		=> 'index',
													  'page'       	=> ''
													  )
											 );
	$frontController->getRouter()->addRoute('frankie100',$frankie100Route);	// Set up the route for the frankie100 page

    // Set up the route for the SwingNation live page
    $date = new DateTime('America/New_York');
    $day =  $date->format('D');

    $shows = array(
        '2014-09-08',
        '2014-09-22',
        '2014-10-06',
        '2014-10-20',
        '2014-11-03',
        '2014-11-17',
        '2014-12-01',
    );

    $time = $date->format('H:i:s');

// For Debugging...
//   if (1 == 1) {
    if (in_array($date->format('Y-m-d'), $shows) && $time >= '21:00:00' && $time <= '23:50:00') {
        // LIVE SHOW
        $swingnationRoute = new Zend_Controller_Router_Route('swingnation/*',
            array('controller'	=> 'swingnation',
                'action'		=> '',
                'page'       	=> ''
            )
        );
    } else {
        // ARCHIVE SHOW
        $swingnationRoute = new Zend_Controller_Router_Route('swingnation/*',
            array('controller'	=> 'show',
                'action'		=> 'swingnation',
                'page'       	=> ''
            )
        );
    }

	$frontController->getRouter()->addRoute('swingnation',$swingnationRoute);

    // Set up the route for the Code of Conduct Page
    $cocRoute = new Zend_Controller_Router_Route('codeofconduct/*',
        array('controller'	=> 'help',
            'action'		=> 'index',
            'page'       	=> 'codeofconduct'
        )
    );
    $frontController->getRouter()->addRoute('codeofconduct',$cocRoute);
