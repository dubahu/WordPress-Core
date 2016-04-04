<?php
/**
 * Simplified WordPress DB drop-in replacement for persistent database connections
 * (If https://core.trac.wordpress.org/attachment/ticket/31018/31018-2.diff gets into core)
 * 
 * Actual working version right now would look like: https://gist.github.com/jtsternberg/eec4ab95e11ce9be4807
 *
 * WordPress Trac Ticket {@link https://core.trac.wordpress.org/ticket/31018}
 */

/**
 * A Subclass of wpdb, a WordPress Database Access Abstraction Object
 *
 * To use, this file needs to be placed in wp-content
 * and will be used instead of wpdb directly.
 *
 * @link http://codex.wordpress.org/Function_Reference/wpdb_Class
 *
 */
class persistent_wpdb extends wpdb {

	/**
	 * A mysqli_real_connect abstraction method. Uses persistent connections.
	 *
	 * @since 4.2.0
	 *
	 * @param string $host         host name or an IP address
	 * @param int    $port         Specifies the port number to attempt to connect to the MySQL server.
	 * @param string $socket       Specifies the socket or named pipe that should be used.
	 * @param int    $client_flags Sets different connection options
	 */
	public function mysqli_real_connect( $host, $port, $socket, $client_flags ) {
		if ( WP_DEBUG ) {
			mysqli_real_connect( $this->dbh, 'p:' . $host, $this->dbuser, $this->dbpassword, null, $port, $socket, $client_flags );
		} else {
			@mysqli_real_connect( $this->dbh, 'p:' . $host, $this->dbuser, $this->dbpassword, null, $port, $socket, $client_flags );
		}
	}

	/**
	 * A mysql_connect abstraction method. Uses persistent connections.
	 *
	 * @since 4.2.0
	 *
	 * @param bool  $new_link     Is this a new link?
	 * @param int    $client_flags Sets different connection options
	 */
	public function mysql_connect( $new_link, $client_flags ) {
		if ( WP_DEBUG ) {
			$this->dbh = mysql_pconnect( $this->dbhost, $this->dbuser, $this->dbpassword, $new_link, $client_flags );
		} else {
			$this->dbh = @mysql_pconnect( $this->dbhost, $this->dbuser, $this->dbpassword, $new_link, $client_flags );
		}
	}

}

$GLOBALS['wpdb'] = new persistent_wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );