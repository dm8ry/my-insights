<?php
$date = new DateTime("now", new DateTimeZone("UTC") );
$cur_dt =  $date->format('d-m-Y H:i:s');  
?>
<!DOCTYPE html>
<html>
<head>
  <title>Dima - Data Digest</title>
  <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no' />
  <link rel="stylesheet" type="text/css" href="assets/lib/bootstrap/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="assets/css/keen-dashboards.css" />
  <link href="assets/css/font-awesome.min.css" rel="stylesheet">
  
	<!-- Favicon and touch icons -->
	<link rel="shortcut icon" href="assets/ico/favicon.png">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
	<link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">  
  
  
  <script type="text/javascript" src="assets/lib/jquery/dist/jquery.min.js"></script>
  <script type="text/javascript" src="assets/lib/bootstrap/dist/js/bootstrap.min.js"></script>  
	
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">

  <?php

	$conn = pg_pconnect("host=10.240.0.20 port=5432 user=postgres dbname=dwh");
	if (!$conn) {
	  echo "An error occurred.\n";
	  exit;
	} 
 
	// get last update date time
 
	$sql_last_upd = "select to_char(max(current_sign_in_at), 'DD-Mon-YYYY HH24:MI:SS') the_dt
						from fct_users 
						where current_sign_in_at>=current_timestamp - interval '2 days'";
	
	$result_last_upd = pg_query($conn, $sql_last_upd);
	if (!$result_last_upd) {
	  echo "An error occurred.\n";
	  exit;
	}	
  
	$arr_last_upd = pg_fetch_all($result_last_upd);
						
 
    // sql_0 - registrations last hours
	
	$sql_0 =  "select to_char(created_at,  'YYYYMMDD HH24')  the_dt, count(1) n_regs 
	           from fct_users where created_at >= date_trunc('hour', current_timestamp - interval '24 hours')
			   group by to_char(created_at,  'YYYYMMDD HH24') 
			   order by  to_char(created_at,  'YYYYMMDD HH24')";
 	
	$result_0 = pg_query($conn, $sql_0);
	if (!$result_0) {
	  echo "An error occurred.\n";
	  exit;
	}	
  
	$arr_0 = pg_fetch_all($result_0);

	
 
    // sql_1 - registrations last 7 days
	
	$sql_1 =  "select to_char(created_at,  'YYYYMMDD')  the_dt, count(1) n_regs 
	           from fct_users 
			   where created_at >= date_trunc('day',current_timestamp - interval '7 days') 
			   group by to_char(created_at,  'YYYYMMDD') 
			   order by  to_char(created_at,  'YYYYMMDD')";
 	
	$result_1 = pg_query($conn, $sql_1);
	if (!$result_1) {
	  echo "An error occurred.\n";
	  exit;
	}	
  
	$arr_1 = pg_fetch_all($result_1);	
 
 
     // sql_2 - registrations yearly 
   
      $sql_2 = "select * from 
					(select to_char(created_at, 'YYYY') yea, count(1) n_of_regs 
					   from fct_users 
					  where created_at >= date_trunc('day', current_timestamp - interval '8 years') 
					  group by to_char(created_at, 'YYYY') 
					  order by to_char(created_at, 'YYYY') 
					  desc limit 6) M 
					  order by 1";
					  
		$result_2 = pg_query($conn, $sql_2);
		if (!$result_2) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_2 = pg_fetch_all($result_2);	
		
 
    // sql_3 - registrations monthly 
   
      $sql_3 = "select * from 
					(select to_char(created_at, 'YY MM') mont, count(1) n_of_regs 
					   from fct_users 
					  where created_at >= date_trunc('day', current_timestamp - interval '360 days') 
					  group by to_char(created_at, 'YY MM') 
					  order by to_char(created_at, 'YY MM') 
					  desc limit 9) M 
					  order by 1";
					  
		$result_3 = pg_query($conn, $sql_3);
		if (!$result_3) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_3 = pg_fetch_all($result_3);	 
 
 
    // sql_4 - registrations weekly 
   
      $sql_4 = "select * from 
					(select to_char(created_at, 'IY IW') week, count(1) n_of_regs 
					   from fct_users 
					  where created_at >= date_trunc('day', current_timestamp - interval '100 days') 
					  group by to_char(created_at, 'IY IW') 
					  order by to_char(created_at, 'IY IW') 
					  desc limit 9) M 
					  order by 1";
					  
		$result_4 = pg_query($conn, $sql_4);
		if (!$result_4) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_4 = pg_fetch_all($result_4);

    // sql_5 - top countries regs last year
   
      $sql_5 = "select country_name, count(1) n_of_users
				from fct_users fu, dim_countries dc
				where fu.country_id = dc.country_id
				and fu.created_at >= date_trunc('day' , current_timestamp - interval '365 days')
				group by country_name
				order by 2 desc
				limit 4";
					  
		$result_5 = pg_query($conn, $sql_5);
		if (!$result_5) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_5 = pg_fetch_all($result_5);	
		
		
		

    // sql_6 - top countries regs last month
   
      $sql_6 = "select country_name, count(1) n_of_users
				from fct_users fu, dim_countries dc
				where fu.country_id = dc.country_id
				and fu.created_at >= date_trunc('day' , current_timestamp - interval '30 days')
				group by country_name
				order by 2 desc
				limit 6";
					  
		$result_6 = pg_query($conn, $sql_6);
		if (!$result_6) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_6 = pg_fetch_all($result_6);		
		
		

    // sql_7 - top countries regs last week
   
      $sql_7 = "select country_name, count(1) n_of_users
				from fct_users fu, dim_countries dc
				where fu.country_id = dc.country_id
				and fu.created_at >= date_trunc('day' , current_timestamp - interval '7 days')
				group by country_name
				order by 2 desc
				limit 6";
					  
		$result_7 = pg_query($conn, $sql_7);
		if (!$result_7) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_7 = pg_fetch_all($result_7);
		
		
		
    // sql_10 - FTDs # recent Days
   
      $sql_10 = "select to_char(ftd_date, 'YYYYMMDD') the_dt, count(1) n_of_ftds
					from fct_24h_ftds
					where ftd_date>=  date_trunc('day', current_timestamp) - interval '7 days'
					group by to_char(ftd_date, 'YYYYMMDD')
					order by to_char(ftd_date, 'YYYYMMDD')";
					  
		$result_10 = pg_query($conn, $sql_10);
		if (!$result_10) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_10 = pg_fetch_all($result_10);		
		
		
		
    // sql_30 - FTDs # Weekly
   
      $sql_30 = "select to_char(ftd_date, 'IY IW') the_dt, count(1) n_of_ftds
				from fct_24h_ftds
				where ftd_date>=  date_trunc('week', current_timestamp) - interval '6 weeks'
				group by to_char(ftd_date, 'IY IW')
				order by to_char(ftd_date, 'IY IW')
				";
					  
		$result_30 = pg_query($conn, $sql_30);
		if (!$result_30) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_30 = pg_fetch_all($result_30);			
		
		
		
		
    // sql_31 - FTDs # Monthly
   
      $sql_31 = "select to_char(ftd_date, 'YY MM') the_dt, count(1) n_of_ftds
					from fct_24h_ftds
					where ftd_date>=  date_trunc('month', current_timestamp) - interval '6 months'
					group by to_char(ftd_date, 'YY MM')
					order by to_char(ftd_date, 'YY MM')";
					  
		$result_31 = pg_query($conn, $sql_31);
		if (!$result_31) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_31 = pg_fetch_all($result_31);			
		
		
	

    // sql_32 - FTDs # Yearly
   
      $sql_32 = "select to_char(ftd_date, 'YYYY') the_dt, count(1) n_of_ftds
					from fct_24h_ftds
					where ftd_date>=  date_trunc('year', current_timestamp) - interval '6 years'
					group by to_char(ftd_date, 'YYYY')
					order by to_char(ftd_date, 'YYYY')";
					  
		$result_32 = pg_query($conn, $sql_32);
		if (!$result_32) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_32 = pg_fetch_all($result_32);		
		
		



    // sql_41 - Deposits recent Days
   
      $sql_41 = "select to_char(deposit_dttime, 'YYYYMMDD') the_dt, 
						round(sum(case when dep_currency = 'USD' then dep_profit else 0 end)) dep_usd,
						round(sum(case when dep_currency = 'EUR' then dep_profit else 0 end)) dep_eur,
						round(sum(case when dep_currency = 'GBP' then dep_profit else 0 end)) dep_gbp
				from fct_deposits
				where deposit_dttime >= date_trunc('day', current_timestamp) - interval '7 days'
				group by to_char(deposit_dttime, 'YYYYMMDD')
				order by to_char(deposit_dttime, 'YYYYMMDD')";
					  
		$result_41 = pg_query($conn, $sql_41);
		if (!$result_41) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_41 = pg_fetch_all($result_41);
		
		
		
		
    // sql_42 - Deposits recent Weeks
   
      $sql_42 = "select to_char(deposit_dttime, 'IY IW') the_dt, 
						round(sum(case when dep_currency = 'USD' then dep_profit else 0 end)) dep_usd,
						round(sum(case when dep_currency = 'EUR' then dep_profit else 0 end)) dep_eur,
						round(sum(case when dep_currency = 'GBP' then dep_profit else 0 end)) dep_gbp
				from fct_deposits
				where deposit_dttime >= date_trunc('week', current_timestamp) - interval '7 weeks'
				group by to_char(deposit_dttime, 'IY IW')
				order by to_char(deposit_dttime, 'IY IW')";
					  
		$result_42 = pg_query($conn, $sql_42);
		if (!$result_42) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_42 = pg_fetch_all($result_42);		
		
		
		
		
    // sql_43 - Deposits recent Months
   
      $sql_43 = "select to_char(deposit_dttime, 'YY MM') the_dt, 
						round(sum(case when dep_currency = 'USD' then dep_profit else 0 end)) dep_usd,
						round(sum(case when dep_currency = 'EUR' then dep_profit else 0 end)) dep_eur,
						round(sum(case when dep_currency = 'GBP' then dep_profit else 0 end)) dep_gbp
				from fct_deposits
				where deposit_dttime >= date_trunc('month', current_timestamp) - interval '7 months'
				group by to_char(deposit_dttime, 'YY MM')
				order by to_char(deposit_dttime, 'YY MM')";
					  
		$result_43 = pg_query($conn, $sql_43);
		if (!$result_43) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_43 = pg_fetch_all($result_43);			
		
		
		

    // sql_44 - Deposits recent Years
   
      $sql_44 = "select to_char(deposit_dttime, 'YYYY') the_dt, 
						round(sum(case when dep_currency = 'USD' then dep_profit else 0 end)) dep_usd,
						round(sum(case when dep_currency = 'EUR' then dep_profit else 0 end)) dep_eur,
						round(sum(case when dep_currency = 'GBP' then dep_profit else 0 end)) dep_gbp
				from fct_deposits
				where deposit_dttime >= date_trunc('year', current_timestamp) - interval '5 years'
				group by to_char(deposit_dttime, 'YYYY')
				order by to_char(deposit_dttime, 'YYYY')";
					  
		$result_44 = pg_query($conn, $sql_44);
		if (!$result_44) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_44 = pg_fetch_all($result_44);		
		

		
    // sql_90 - Trades # recent Hours
   
      $sql_90 = "select *
				from
				(select to_char(close_time, 'YYYYMMDD HH24') the_dt, 
				  count(1) n_of_trades, 
				  round(sum(trade_close_volume_usd)) tr_close_vol_usd, 
				  round(sum(trade_close_profit_usd)) tr_close_prof_usd,
				  round(sum(trade_revenue_usd)) tr_rev_usd
				from fct_trades
				where close_time >= date_trunc('hour', current_timestamp) - interval '80 hours'
				group by to_char(close_time, 'YYYYMMDD HH24')
				order by to_char(close_time, 'YYYYMMDD HH24') desc
				limit 10) M
				order by the_dt";
					  
		$result_90 = pg_query($conn, $sql_90);
		if (!$result_90) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_90 = pg_fetch_all($result_90);			
		
		
	

    // sql_91 - Trades # recent Days
   
      $sql_91 = "select to_char(close_time, 'YYYYMMDD') the_dt, 
	              count(1) n_of_trades,				  
				  round(sum(trade_close_volume_usd)) tr_close_vol_usd, 
				  round(sum(trade_close_profit_usd)) tr_close_prof_usd,
				  round(sum(trade_revenue_usd)) tr_rev_usd
				from fct_trades
				where close_time >= date_trunc('day', current_timestamp) - interval '8 days'
				group by to_char(close_time, 'YYYYMMDD')
				order by to_char(close_time, 'YYYYMMDD')  ";
					  
		$result_91 = pg_query($conn, $sql_91);
		if (!$result_91) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_91 = pg_fetch_all($result_91);		
		
		
		
		
		
    // sql_93 - Trades # recent Weeks
   
      $sql_93 = "select to_char(close_time, 'IY IW') the_dt, 
	              count(1) n_of_trades,				  
				  round(sum(trade_close_volume_usd)) tr_close_vol_usd, 
				  round(sum(trade_close_profit_usd)) tr_close_prof_usd,
				  round(sum(trade_revenue_usd)) tr_rev_usd
				from fct_trades
				where close_time >= date_trunc('week', current_timestamp) - interval '4 weeks'
				group by to_char(close_time, 'IY IW')
				order by to_char(close_time, 'IY IW')  ";
					  
		$result_93 = pg_query($conn, $sql_93);
		if (!$result_93) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_93 = pg_fetch_all($result_93);			
		
		
    // sql_94 - Trades # recent Months
   
      $sql_94 = "select to_char(close_time, 'YY MM') the_dt, 
	              count(1) n_of_trades,				  
				  round(sum(trade_close_volume_usd)) tr_close_vol_usd, 
				  round(sum(trade_close_profit_usd)) tr_close_prof_usd,
				  round(sum(trade_revenue_usd)) tr_rev_usd
				from fct_trades
				where close_time >= date_trunc('month', current_timestamp) - interval '5 months'
				group by to_char(close_time, 'YY MM')
				order by to_char(close_time, 'YY MM')  ";
					  
		$result_94 = pg_query($conn, $sql_94);
		if (!$result_94) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_94 = pg_fetch_all($result_94);		
		
		
		
		
		
    // sql_95 - Trades # recent Years
   
      $sql_95 = "select to_char(close_time, 'YYYY') the_dt, 
	              count(1) n_of_trades,				  
				  round(sum(trade_close_volume_usd)) tr_close_vol_usd, 
				  round(sum(trade_close_profit_usd)) tr_close_prof_usd,
				  round(sum(trade_revenue_usd)) tr_rev_usd
				from fct_trades
				where close_time >= date_trunc('year', current_timestamp) - interval '2 years'
				group by to_char(close_time, 'YYYY')
				order by to_char(close_time, 'YYYY')  ";
					  
		$result_95 = pg_query($conn, $sql_95);
		if (!$result_95) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_95 = pg_fetch_all($result_95);				
		
		
		
		
		
	// sql_61 - top countries ftds count last week	
	
	$sql_61 = "select dc.country_name, count(distinct f2f.acc_id) num_of_ftds
				from fct_24h_ftds  f2f
				join fct_accounts fa on fa.acc_id = f2f.acc_id
				join fct_users fu on fu.us_id = fa.general_user_id
				join dim_countries dc on dc.country_id = fu.country_id
				where f2f.ftd_date >= current_timestamp -  interval '7 days'
				group by dc.country_name
				order by 2 desc
				limit 5";
		
	$result_61 = pg_query($conn, $sql_61);
	if (!$result_61) {
	  echo "An error occurred.\n";
	  exit;
	}	
  
	$arr_61 = pg_fetch_all($result_61);		
		
		
		
	// sql_62 - top countries ftds count last month	
	
	$sql_62 = "select dc.country_name, count(distinct f2f.acc_id) num_of_ftds
				from fct_24h_ftds  f2f
				join fct_accounts fa on fa.acc_id = f2f.acc_id
				join fct_users fu on fu.us_id = fa.general_user_id
				join dim_countries dc on dc.country_id = fu.country_id
				where f2f.ftd_date >= current_timestamp -  interval '30 days'
				group by dc.country_name
				order by 2 desc
				limit 5";
		
	$result_62 = pg_query($conn, $sql_62);
	if (!$result_62) {
	  echo "An error occurred.\n";
	  exit;
	}	
  
	$arr_62 = pg_fetch_all($result_62);			
		
		
		
		
	// sql_63 - top countries ftds count last year	
	
	$sql_63 = "select dc.country_name, count(distinct f2f.acc_id) num_of_ftds
				from fct_24h_ftds  f2f
				join fct_accounts fa on fa.acc_id = f2f.acc_id
				join fct_users fu on fu.us_id = fa.general_user_id
				join dim_countries dc on dc.country_id = fu.country_id
				where f2f.ftd_date >= current_timestamp -  interval '365 days'
				group by dc.country_name
				order by 2 desc
				limit 5";
		
	$result_63 = pg_query($conn, $sql_63);
	if (!$result_63) {
	  echo "An error occurred.\n";
	  exit;
	}	
  
	$arr_63 = pg_fetch_all($result_63);			
		
		
		
    // sql_80 - online users last hours
	
	$sql_80 =  "select to_char(current_sign_in_at,  'DD HH24')  the_dt, count(1) n_online_users 
	           from fct_users where current_sign_in_at >= date_trunc('hour', current_timestamp - interval '24 hours')
			   group by to_char(current_sign_in_at,  'DD HH24') 
			   order by  to_char(current_sign_in_at,  'DD HH24')";
 	
	$result_80 = pg_query($conn, $sql_80);
	if (!$result_80) {
	  echo "An error occurred.\n";
	  exit;
	}	
  
	$arr_80 = pg_fetch_all($result_80);
		
		
    // sql_81 - online users last days
	
	$sql_81 =  "select to_char(current_sign_in_at,  'YYYYMMDD')  the_dt, count(1) n_online_users 
	           from fct_users where current_sign_in_at >= date_trunc('day', current_timestamp - interval '7 days')
			   group by to_char(current_sign_in_at,  'YYYYMMDD') 
			   order by  to_char(current_sign_in_at,  'YYYYMMDD')";
 	
	$result_81 = pg_query($conn, $sql_81);
	if (!$result_81) {
	  echo "An error occurred.\n";
	  exit;
	}	
  
	$arr_81 = pg_fetch_all($result_81);	



    // sql_83 - online users weekly
	
	$sql_83 =  "select to_char(current_sign_in_at,  'IY IW')  the_dt, count(1) n_online_users 
	           from fct_users where current_sign_in_at >= date_trunc('week', current_timestamp - interval '6 weeks')
			   group by to_char(current_sign_in_at,  'IY IW') 
			   order by  to_char(current_sign_in_at,  'IY IW')";
 	
	$result_83 = pg_query($conn, $sql_83);
	if (!$result_83) {
	  echo "An error occurred.\n";
	  exit;
	}	
  
	$arr_83 = pg_fetch_all($result_83);	


    // sql_84 - online users monthly
	
	$sql_84 =  "select to_char(current_sign_in_at,  'YY MM')  the_dt, count(1) n_online_users 
	           from fct_users where current_sign_in_at >= date_trunc('month', current_timestamp - interval '6 months')
			   group by to_char(current_sign_in_at,  'YY MM') 
			   order by  to_char(current_sign_in_at,  'YY MM')";
 	
	$result_84 = pg_query($conn, $sql_84);
	if (!$result_84) {
	  echo "An error occurred.\n";
	  exit;
	}	
  
	$arr_84 = pg_fetch_all($result_84);		
		
 
 
 
    // sql_85- online users yearly
	
	$sql_85 =  "select to_char(current_sign_in_at,  'YYYY')  the_dt, count(1) n_online_users 
	           from fct_users where current_sign_in_at >= date_trunc('year', current_timestamp - interval '6 years')
			   group by to_char(current_sign_in_at,  'YYYY') 
			   order by  to_char(current_sign_in_at,  'YYYY')";
 	
	$result_85 = pg_query($conn, $sql_85);
	if (!$result_85) {
	  echo "An error occurred.\n";
	  exit;
	}	
  
	$arr_85 = pg_fetch_all($result_85);	
	
 
 
 
 
 
     // sql_500 -- ytd regs # vs prev year
	 
		$sql_500 = "select current_year.n_regs curr_y_regs , 
							prev_year.n_regs prev_y_regs , 
							round(100 * current_year.n_regs/prev_year.n_regs) pct_change
							from 
							(select count(1) n_regs
							from fct_users
							where created_at >= date_trunc('year', current_timestamp)) current_year,
							(select count(1) n_regs
							from fct_users
							where created_at >= date_trunc('year', current_timestamp - interval '1 year') 
									and created_at < date_trunc('year', current_timestamp)) prev_year ";
 
		$result_500 = pg_query($conn, $sql_500);
		if (!$result_500) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_500 = pg_fetch_all($result_500); 
 
 
 
 
     // sql_501 -- YTD FTDs # vs Prev. Year
	 
		$sql_501 = "select current_year.n_ftds curr_y_ftds, prev_year.n_ftds prev_y_ftds, round(100 * current_year.n_ftds/prev_year.n_ftds) pct_change
					from 
					(select count(1) n_ftds
					from fct_24h_ftds
					where ftd_date >= date_trunc('year', current_timestamp)) current_year,
					(select count(1) n_ftds
					from fct_24h_ftds
					where ftd_date >= date_trunc('year', current_timestamp - interval '1 year') 
					    and ftd_date < date_trunc('year', current_timestamp)) prev_year";
					 
		$result_501 = pg_query($conn, $sql_501);
		if (!$result_501) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_501 = pg_fetch_all($result_501); 
		
 
 
 
     // sql_502 -- YTD Deposits Amt EUR # vs Prev. Year
	 
		$sql_502 = "select current_year.amt_eur curr_y_depeur, prev_year.amt_eur prev_y_depeur, round(100 * current_year.amt_eur/prev_year.amt_eur) pct_change
					from 
					(select round(sum(deposit_in_eur)) amt_eur
					from fct_deposits
					where deposit_dttime >= date_trunc('year', current_timestamp)) current_year,
					(select round(sum(deposit_in_eur)) amt_eur
					from fct_deposits
					where deposit_dttime >= date_trunc('year', current_timestamp - interval '1 year') 
					and deposit_dttime < date_trunc('year', current_timestamp)) prev_year";
					 
		$result_502 = pg_query($conn, $sql_502);
		if (!$result_502) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_502 = pg_fetch_all($result_502);  
 
 
 
     // sql_503 -- YTD Trades Volumes USD # vs Prev. Year (%)
	 
		$sql_503 = "select current_year.vol_usd curr_y_vol_usd, prev_year.vol_usd prev_y_vol_usd, round(100 * current_year.vol_usd/prev_year.vol_usd) pct_change
		from 
		(select round(sum(trade_close_volume_usd)) vol_usd
		from fct_trades
		where close_time >= date_trunc('year', current_timestamp)) current_year,
		(select round(sum(trade_close_volume_usd)) vol_usd
		from fct_trades
		where close_time >= date_trunc('year', current_timestamp - interval '1 year') and close_time < date_trunc('year', current_timestamp)) prev_year";
							 
		$result_503 = pg_query($conn, $sql_503);
		if (!$result_503) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_503 = pg_fetch_all($result_503);  
 
 
 
     // sql_504 -- YTD Trades Profit USD vs Prev. Year
	 
		$sql_504 = "select current_year.closed_profit_usd curr_y_prof_usd, 
					prev_year.closed_profit_usd prev_y_prof_usd, 
					round(100 * current_year.closed_profit_usd/prev_year.closed_profit_usd) pct_change
					from 
					(select round(sum(trade_close_profit_usd)) closed_profit_usd
					from fct_trades
					where close_time >= date_trunc('year', current_timestamp)) current_year,
					(select round(sum(trade_close_profit_usd)) closed_profit_usd
					from fct_trades
					where close_time >= date_trunc('year', current_timestamp - interval '1 year') 
					and close_time < date_trunc('year', current_timestamp)) prev_year ";
							 
		$result_504 = pg_query($conn, $sql_504);
		if (!$result_504) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_504 = pg_fetch_all($result_504);   
 
 

 
      // sql_505 -- YTD Trades Rev USD vs Prev. Year
	 
		$sql_505 = "select 
						current_year.closed_rev_usd curr_y_rev_usd, 
						prev_year.closed_rev_usd prev_y_rev_usd, 
						round(100 * current_year.closed_rev_usd/prev_year.closed_rev_usd) pct_change
					from 
					(select round(sum(trade_revenue_usd)) closed_rev_usd
					from fct_trades
					where close_time >= date_trunc('year', current_timestamp)) current_year,
					(select round(sum(trade_revenue_usd)) closed_rev_usd
					from fct_trades
					where close_time >= date_trunc('year', current_timestamp - interval '1 year') 
					and close_time < date_trunc('year', current_timestamp)) prev_year ";
							 
		$result_505 = pg_query($conn, $sql_505);
		if (!$result_505) {
		  echo "An error occurred.\n";
		  exit;
		}	
	  
		$arr_505 = pg_fetch_all($result_505); 
 
 
 
 
 
  ?>
  
	  google.charts.load('current', {'packages':['corechart', 'line', 'bar', 'gauge']});
	  
	  google.charts.setOnLoadCallback(drawChart);		
 
	  function drawChart() {
		
		
		/// 0 -- registrations recent hours (sql_0)
		
        var data0 = google.visualization.arrayToDataTable([
          ['Hour', 'Regs #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_0); $idx++)
		  {				
				echo '[\''.substr($arr_0[$idx]['the_dt'], 9, 2).'\','.$arr_0[$idx]['n_regs'].','.$arr_0[$idx]['n_regs'].'], ';
		  }
		  
		  ?>
        ]);	
	   
        var options0 = {
          title: 'Regs # recent Hours',
          hAxis: {title: 'Hours',  titleTextStyle: {color: '#e34352'}},
          vAxis: {minValue: 0},
		  colors: ['#e34352']
        };					   

		// Instantiate and draw our chart, passing in some options.
		var chart0 = new google.visualization.AreaChart(document.getElementById('chart_div0'));
		chart0.draw(data0, options0);
		
		
		/// 1 -- registrations last 7 days (sql_1)
		
        var data = google.visualization.arrayToDataTable([
          ['Date', 'Regs #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_1); $idx++)
		  {		  
			echo '[\''.$arr_1[$idx]['the_dt'].'\','.$arr_1[$idx]['n_regs'].','.$arr_1[$idx]['n_regs'].'], ';
		  }
		  
		  ?>
        ]);	
	   
        var options = {
          title: 'Regs # recent Days',
          hAxis: {title: 'Days',  titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0}
        };					   

		// Instantiate and draw our chart, passing in some options.
		var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
		chart.draw(data, options);
		
		//// 2 -- registrations yearly
		
        var data2 = google.visualization.arrayToDataTable([
          ['Year', 'Regs #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_2); $idx++)
		  {		  
			echo '[\''.$arr_2[$idx]['yea'].'\','.$arr_2[$idx]['n_of_regs'].','.$arr_2[$idx]['n_of_regs'].'], ';
		  }
		  
		  ?>
        ]);	

		var options2 = {
          title: 'Regs # Yearly',
		  hAxis: {title: 'Year', titleTextStyle: {color: '#1b7eac'}},          
          legend: { position: 'none' },
		  colors: ['#1b7eac']
        };		
		
		var chart2 = new google.visualization.ColumnChart(document.getElementById('chart_div2'));
        chart2.draw(data2, options2);	
		
		
		//// 3 -- registrations monthly
		
        var data3 = google.visualization.arrayToDataTable([
          ['Month', 'Regs #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_3); $idx++)
		  {		  
			echo '[\''.substr($arr_3[$idx]['mont'], 3, 2).'\','.$arr_3[$idx]['n_of_regs'].','.$arr_3[$idx]['n_of_regs'].'], ';
		  }
		  
		  ?>
        ]);	

		var options3 = {
          title: 'Regs # Montly',  
		  hAxis: {title: 'Month', titleTextStyle: {color: '#9dc62d'}}, 
          legend: { position: 'none' },
		  colors: ['#9dc62d']
        };		
		
		var chart3 = new google.visualization.ColumnChart(document.getElementById('chart_div3'));
        chart3.draw(data3, options3);			
		
	

		//// 4 -- registrations weekly
		
        var data4 = google.visualization.arrayToDataTable([
          ['Week', 'Regs #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_4); $idx++)
		  {		  
			echo '[\''.substr($arr_4[$idx]['week'], 3, 2).'\','.$arr_4[$idx]['n_of_regs'].','.$arr_4[$idx]['n_of_regs'].'], ';
		  }
		  
		  ?>
        ]);	

		var options4 = {
          title: 'Regs # Weekly',  
		  hAxis: {title: 'Week', titleTextStyle: {color: '#dd8e07'}}, 		  
          legend: { position: 'none' },
		  colors: ['#dd8e07']		 
        };		
		
		var chart4= new google.visualization.ColumnChart(document.getElementById('chart_div4'));
        chart4.draw(data4, options4);
		
		
		
		
		
		//// 5 -- top 5 countries registrations # yearly
		
        var data5 = google.visualization.arrayToDataTable([
          ['Country', 'Regs #'],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_5); $idx++)
		  {		  
			echo '[\''.substr($arr_5[$idx]['country_name'], 0, 13).'\','.$arr_5[$idx]['n_of_users'].'], ';
		  }
		  
		  ?>
        ]);	

		var options5 = {
          title: 'Top Countries Year Regs#',  
		  hAxis: {title: 'Country'}, 		           
		  is3D: true,
		  pieSliceText: 'value'
        };		
		
		var chart5= new google.visualization.PieChart(document.getElementById('chart_div5'));
        chart5.draw(data5, options5);		
		
		//// 6 -- top 6 countries registrations # monthly
		
        var data6 = google.visualization.arrayToDataTable([
          ['Country', 'Regs #'],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_6); $idx++)
		  {		  
			echo '[\''.substr($arr_6[$idx]['country_name'], 0, 13).'\','.$arr_6[$idx]['n_of_users'].'], ';
		  }
		  
		  ?>
        ]);	

		var options6 = {
          title: 'Top Countries Month Regs#',  
		  hAxis: {title: 'Country'}, 		           
		  is3D: true,
		  pieSliceText: 'value'
        };		
		
		var chart6= new google.visualization.PieChart(document.getElementById('chart_div6'));
        chart6.draw(data6, options6);		
		
		//// 7 -- top 6 countries registrations # weekly
		
        var data7 = google.visualization.arrayToDataTable([
          ['Country', 'Regs #'],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_7); $idx++)
		  {		  
			echo '[\''.substr($arr_7[$idx]['country_name'], 0, 13).'\','.$arr_7[$idx]['n_of_users'].'], ';
		  }
		  
		  ?>
        ]);	

		var options7 = {
          title: 'Top Countries Week Regs#',  
		  hAxis: {title: 'Country'}, 		           
		  is3D: true,
		  pieSliceText: 'value'
        };		
		
		var chart7= new google.visualization.PieChart(document.getElementById('chart_div7'));
        chart7.draw(data7, options7);			
		
		
		/// 10 -- FTDs (count) recent week
		
        var data10 = google.visualization.arrayToDataTable([
          ['Date', 'FTDs #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_10); $idx++)
		  {		  
			echo '[\''.$arr_10[$idx]['the_dt'].'\','.$arr_10[$idx]['n_of_ftds'].','.$arr_10[$idx]['n_of_ftds'].'], ';
		  }
		  
		  ?>
        ]);	
	   
        var options10 = {
          title: 'FTDs (count) recent Days',
          hAxis: {title: 'Days',  titleTextStyle: {color: '#e34352'}},
          vAxis: {minValue: 0},
		  colors: ['#e34352']
        };					   

		// Instantiate and draw our chart, passing in some options.
		var chart10 = new google.visualization.AreaChart(document.getElementById('chart_div10'));
		chart10.draw(data10, options10);		
		
			
		
		
		//// 30 -- FTDs weekly
		
        var data30 = google.visualization.arrayToDataTable([
          ['Week', 'FTDs #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_30); $idx++)
		  {		  
			echo '[\''.substr($arr_30[$idx]['the_dt'], 3, 2).'\','.$arr_30[$idx]['n_of_ftds'].','.$arr_30[$idx]['n_of_ftds'].'], ';
		  }
		  
		  ?>
        ]);	

		var options30 = {
          title: 'FTDs (count) Weekly',  
		  hAxis: {title: 'Week', titleTextStyle: {color: '#dd8e07'}}, 		  
          legend: { position: 'none' },
		  colors: ['#dd8e07']		 
        };		
		
		var chart30= new google.visualization.ColumnChart(document.getElementById('chart_div30'));
        chart30.draw(data30, options30);
		
		
		
		//// 31 -- FTDs monthly
		
        var data31 = google.visualization.arrayToDataTable([
          ['Month', 'FTDs #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_31); $idx++)
		  {		  
			echo '[\''.substr($arr_31[$idx]['the_dt'], 3, 2).'\','.$arr_31[$idx]['n_of_ftds'].','.$arr_31[$idx]['n_of_ftds'].'], ';
		  }
		  
		  ?>
        ]);	

		var options31 = {
          title: 'FTDs (count) Montly',  
		  hAxis: {title: 'Month', titleTextStyle: {color: '#9dc62d'}}, 
          legend: { position: 'none' },
		  colors: ['#9dc62d']
        };		
		
		var chart31 = new google.visualization.ColumnChart(document.getElementById('chart_div31'));
        chart31.draw(data31, options31);			
		
	
				
		//// 32 -- registrations yearly
		
        var data32 = google.visualization.arrayToDataTable([
          ['Year', 'FTDs #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_32); $idx++)
		  {		  
			echo '[\''.$arr_32[$idx]['the_dt'].'\','.$arr_32[$idx]['n_of_ftds'].','.$arr_32[$idx]['n_of_ftds'].'], ';
		  }
		  
		  ?>
        ]);	

		var options32 = {
          title: 'FTDs (count) Yearly',
		  hAxis: {title: 'Year', titleTextStyle: {color: '#1b7eac'}},          
          legend: { position: 'none' },
		  colors: ['#1b7eac']
        };		
		
		var chart32 = new google.visualization.ColumnChart(document.getElementById('chart_div32'));
        chart32.draw(data32, options32);
				
		
		// 41 Deposits recent Days
		
		var data41 = google.visualization.arrayToDataTable([
				  ['Date', 'USD', {type: 'string', role: 'annotation'}, 'EUR',  {type: 'string', role: 'annotation'}, 'GBP',  {type: 'string', role: 'annotation'}],
				  <?php
				   for ($idx=0; $idx< sizeof($arr_41); $idx++)
				   {
				 	echo '[\''.$arr_41[$idx]['the_dt'].'\','.$arr_41[$idx]['dep_usd'].','.$arr_41[$idx]['dep_usd'].','.$arr_41[$idx]['dep_eur'].','.$arr_41[$idx]['dep_eur'].','.$arr_41[$idx]['dep_gbp'].','.$arr_41[$idx]['dep_gbp'].'], ';
				   }
				  ?>
			  ]);

			var options41 = {			  
			  vAxis: {title: 'Amount'},
			  hAxis: {title: 'Days'},
			  seriesType: 'bars',
			  series: {5: {type: 'line'}}
			};

			var chart41 = new google.visualization.ComboChart(document.getElementById('chart_div41'));
			chart41.draw(data41, options41);		
		
		// 42 Deposits recent Weeks
		
		var data42 = google.visualization.arrayToDataTable([
				  ['Week', 'USD', {type: 'string', role: 'annotation'}, 'EUR',  {type: 'string', role: 'annotation'}, 'GBP',  {type: 'string', role: 'annotation'}],
				  <?php
				   for ($idx=0; $idx< sizeof($arr_42); $idx++)
				   {
				 	echo '[\''.substr($arr_42[$idx]['the_dt'], 3, 2).'\','.$arr_42[$idx]['dep_usd'].','.$arr_42[$idx]['dep_usd'].','.$arr_42[$idx]['dep_eur'].','.$arr_42[$idx]['dep_eur'].','.$arr_42[$idx]['dep_gbp'].','.$arr_42[$idx]['dep_gbp'].'], ';
				   }
				  ?>
			  ]);

			var options42 = {			  
			  vAxis: {title: 'Amount'},
			  hAxis: {title: 'Weeks'},
			  seriesType: 'bars',
			  series: {5: {type: 'line'}}
			};

			var chart42 = new google.visualization.ComboChart(document.getElementById('chart_div42'));
			chart42.draw(data42, options42);		
		
		
		// 43 Deposits recent Months
		
		var data43 = google.visualization.arrayToDataTable([
				  ['Date', 'USD', {type: 'string', role: 'annotation'}, 'EUR',  {type: 'string', role: 'annotation'}, 'GBP',  {type: 'string', role: 'annotation'}],
				  <?php
				   for ($idx=0; $idx< sizeof($arr_43); $idx++)
				   {
				 	echo '[\''.substr($arr_43[$idx]['the_dt'], 3, 2).'\','.$arr_43[$idx]['dep_usd'].','.$arr_43[$idx]['dep_usd'].','.$arr_43[$idx]['dep_eur'].','.$arr_43[$idx]['dep_eur'].','.$arr_43[$idx]['dep_gbp'].','.$arr_43[$idx]['dep_gbp'].'], ';
				   }
				  ?>
			  ]);

			var options43 = {			  
			  vAxis: {title: 'Amount'},
			  hAxis: {title: 'Months'},
			  seriesType: 'bars',
			  series: {5: {type: 'line'}}
			};

			var chart43 = new google.visualization.ComboChart(document.getElementById('chart_div43'));
			chart43.draw(data43, options43);			
		
		
		// 44 Deposits recent Years
		
		var data44 = google.visualization.arrayToDataTable([
				  ['Date', 'USD', {type: 'string', role: 'annotation'}, 'EUR',  {type: 'string', role: 'annotation'}, 'GBP',  {type: 'string', role: 'annotation'}],
				  <?php
				   for ($idx=0; $idx< sizeof($arr_44); $idx++)
				   {
				 	echo '[\''.$arr_44[$idx]['the_dt'].'\','.$arr_44[$idx]['dep_usd'].','.$arr_44[$idx]['dep_usd'].','.$arr_44[$idx]['dep_eur'].','.$arr_44[$idx]['dep_eur'].','.$arr_44[$idx]['dep_gbp'].','.$arr_44[$idx]['dep_gbp'].'], ';
				   }
				  ?>
			  ]);

			var options44 = {			  
			  vAxis: {title: 'Amount'},
			  hAxis: {title: 'Years'},
			  seriesType: 'bars',
			  series: {5: {type: 'line'}}
			};

			var chart44 = new google.visualization.ComboChart(document.getElementById('chart_div44'));
			chart44.draw(data44, options44);	

			
		
		
		/// 80 -- Online Users # recent Hours (sql_80)
		
        var data80 = google.visualization.arrayToDataTable([
          ['Hour', 'Online Users #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_80); $idx++)
		  {				
				echo '[\''.substr($arr_80[$idx]['the_dt'], 3, 2).'\','.$arr_80[$idx]['n_online_users'].','.$arr_80[$idx]['n_online_users'].'], ';
		  }
		  
		  ?>
        ]);	
	   
        var options80 = {
          title: 'Online Users # recent Hours',
          hAxis: {title: 'Hours',  titleTextStyle: {color: '#5c832f'}},
          vAxis: {minValue: 0},
		  colors: ['#5c832f']
        };					   

		// Instantiate and draw our chart, passing in some options.
		var chart80 = new google.visualization.AreaChart(document.getElementById('chart_div80'));
		chart80.draw(data80, options80);		
		
		
		/// 81 -- Online Users # recent Weeks (sql_81)
		
        var data81 = google.visualization.arrayToDataTable([
          ['Week', 'Online Users #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_81); $idx++)
		  {				
				echo '[\''.$arr_81[$idx]['the_dt'].'\','.$arr_81[$idx]['n_online_users'].','.$arr_81[$idx]['n_online_users'].'], ';
		  }
		  
		  ?>
        ]);	
	   
        var options81 = {
          title: 'Online Users # recent Days',
          hAxis: {title: 'Days',  titleTextStyle: {color: '#493862'}},
          vAxis: {minValue: 0},
		  colors: ['#493862']
        };					   

		// Instantiate and draw our chart, passing in some options.
		var chart81 = new google.visualization.AreaChart(document.getElementById('chart_div81'));
		chart81.draw(data81, options81);		
		
		
		
		//// 83 -- online users weekly
		
        var data83 = google.visualization.arrayToDataTable([
          ['Week', 'Online Users #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_83); $idx++)
		  {		  
			echo '[\''.substr($arr_83[$idx]['the_dt'], 3, 2).'\','.$arr_83[$idx]['n_online_users'].','.$arr_83[$idx]['n_online_users'].'], ';
		  }
		  
		  ?>
        ]);	

		var options83 = {
          title: 'Online Users # Weekly',  
		  hAxis: {title: 'Week', titleTextStyle: {color: '#dd8e07'}}, 
          legend: { position: 'none' },
		  colors: ['#dd8e07']
        };		
		
		var chart83 = new google.visualization.ColumnChart(document.getElementById('chart_div83'));
        chart83.draw(data83, options83);			
		
		
		
		//// 84 -- online users monthly
		
        var data84 = google.visualization.arrayToDataTable([
          ['Month', 'Online Users #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_84); $idx++)
		  {		  
			echo '[\''.substr($arr_84[$idx]['the_dt'], 3, 2).'\','.$arr_84[$idx]['n_online_users'].','.$arr_84[$idx]['n_online_users'].'], ';
		  }
		  
		  ?>
        ]);	

		var options84 = {
          title: 'Online Users # Monthly',  
		  hAxis: {title: 'Month', titleTextStyle: {color: '#9dc62d'}}, 
          legend: { position: 'none' },
		  colors: ['#9dc62d']
        };		
		
		var chart84 = new google.visualization.ColumnChart(document.getElementById('chart_div84'));
        chart84.draw(data84, options84);		
		
		
		
		
		//// 85 -- online users yearly
		
        var data85 = google.visualization.arrayToDataTable([
          ['Year', 'Online Users #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_85); $idx++)
		  {		  
			echo '[\''.$arr_85[$idx]['the_dt'].'\','.$arr_85[$idx]['n_online_users'].','.$arr_85[$idx]['n_online_users'].'], ';
		  }
		  
		  ?>
        ]);	

		var options85 = {
          title: 'Online Users # Yearly',  
		  hAxis: {title: 'Year', titleTextStyle: {color: '#1b7eac'}}, 
          legend: { position: 'none' },
		  colors: ['#1b7eac']
        };		
		
		var chart85 = new google.visualization.ColumnChart(document.getElementById('chart_div85'));
        chart85.draw(data85, options85);			
		
		
		
		
		//// 61 -- Top Countries FTDs # Last Week
		
        var data61 = google.visualization.arrayToDataTable([
          ['Country', 'FTDs #'],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_61); $idx++)
		  {		  
			echo '[\''.substr($arr_61[$idx]['country_name'], 0, 13).'\','.$arr_61[$idx]['num_of_ftds'].'], ';
		  }
		  
		  ?>
        ]);	

		var options61 = {
          title: 'Top Countries Week FTDs#',  
		  hAxis: {title: 'Country'}, 		           
		  is3D: true,
		  pieSliceText: 'value'
        };		
		
		var chart61= new google.visualization.PieChart(document.getElementById('chart_div61'));
        chart61.draw(data61, options61);				
		
		
		
		
		
		//// 62 -- Top Countries FTDs # Last Month
		
        var data62 = google.visualization.arrayToDataTable([
          ['Country', 'FTDs #'],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_62); $idx++)
		  {		  
			echo '[\''.substr($arr_62[$idx]['country_name'], 0, 13).'\','.$arr_62[$idx]['num_of_ftds'].'], ';
		  }
		  
		  ?>
        ]);	

		var options62 = {
          title: 'Top Countries Month FTDs#',  
		  hAxis: {title: 'Country'}, 		           
		  is3D: true,
		  pieSliceText: 'value'
        };		
		
		var chart62= new google.visualization.PieChart(document.getElementById('chart_div62'));
        chart62.draw(data62, options62);

		
		
		
		//// 63 -- Top Countries FTDs # Last Year
		
        var data63 = google.visualization.arrayToDataTable([
          ['Country', 'FTDs #'],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_63); $idx++)
		  {		  
			echo '[\''.substr($arr_63[$idx]['country_name'], 0, 13).'\','.$arr_63[$idx]['num_of_ftds'].'], ';
		  }
		  
		  ?>
        ]);	

		var options63 = {
          title: 'Top Countries Year FTDs#',  
		  hAxis: {title: 'Country'}, 		           
		  is3D: true,
		  pieSliceText: 'value'
        };		
		
		var chart63= new google.visualization.PieChart(document.getElementById('chart_div63'));
        chart63.draw(data63, options63);
		
		
		
		
		/// 90 -- Trades # recent Hours
		
        var data90 = google.visualization.arrayToDataTable([
          ['Hour', 'Trades #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_90); $idx++)
		  {		  
			echo '[\''.substr($arr_90[$idx]['the_dt'], 9, 2).'\','.$arr_90[$idx]['n_of_trades'].','.$arr_90[$idx]['n_of_trades'].'], ';
		  }
		  
		  ?>
        ]);	
	   
        var options90 = {
          title: 'Trades # recent Hours',
          hAxis: {title: 'Hours',  titleTextStyle: {color: '#e34352'}},
          vAxis: {minValue: 0},
		  colors: ['#e34352']
        };					   

		// Instantiate and draw our chart, passing in some options.
		var chart90 = new google.visualization.AreaChart(document.getElementById('chart_div90'));
		chart90.draw(data90, options90);		
		
		
		
		/// 91 -- Trades # recent Days
		
        var data91 = google.visualization.arrayToDataTable([
          ['Day', 'Trades #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_91); $idx++)
		  {		  
			echo '[\''.$arr_91[$idx]['the_dt'].'\','.$arr_91[$idx]['n_of_trades'].','.$arr_91[$idx]['n_of_trades'].'], ';
		  }
		  
		  ?>
        ]);	
	   
        var options91 = {
          title: 'Trades # recent Days',
          hAxis: {title: 'Days',  titleTextStyle: {color: '#1b7eac'}},
          vAxis: {minValue: 0},
		  colors: ['#1b7eac']
        };					   

		// Instantiate and draw our chart, passing in some options.
		var chart91 = new google.visualization.AreaChart(document.getElementById('chart_div91'));
		chart91.draw(data91, options91);		
		
		
		
		
		//// 93 -- Trades weekly
		
        var data93 = google.visualization.arrayToDataTable([
          ['Week', 'Trades #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_93); $idx++)
		  {		  
			echo '[\''.substr($arr_93[$idx]['the_dt'], 3, 2).'\','.$arr_93[$idx]['n_of_trades'].','.$arr_93[$idx]['n_of_trades'].'], ';
		  }
		  
		  ?>
        ]);	

		var options93 = {
          title: 'Trades # Weekly',  
		  hAxis: {title: 'Week', titleTextStyle: {color: '#dd8e07'}}, 		  
          legend: { position: 'none' },
		  colors: ['#dd8e07']		 
        };		
		
		var chart93= new google.visualization.ColumnChart(document.getElementById('chart_div93'));
        chart93.draw(data93, options93);
		
		
		
		//// 94 -- Trades monthly
		
        var data94 = google.visualization.arrayToDataTable([
          ['Month', 'Trades #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_94); $idx++)
		  {		  
			echo '[\''.substr($arr_94[$idx]['the_dt'], 3, 2).'\','.$arr_94[$idx]['n_of_trades'].','.$arr_94[$idx]['n_of_trades'].'], ';
		  }
		  
		  ?>
        ]);	

		var options94 = {
          title: 'Trades # Montly',  
		  hAxis: {title: 'Month', titleTextStyle: {color: '#9dc62d'}}, 
          legend: { position: 'none' },
		  colors: ['#9dc62d']
        };		
		
		var chart94 = new google.visualization.ColumnChart(document.getElementById('chart_div94'));
        chart94.draw(data94, options94);			
		
	
				
		//// 95 -- Trades yearly
		
        var data95 = google.visualization.arrayToDataTable([
          ['Year', 'Trades #', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_95); $idx++)
		  {		  
			echo '[\''.$arr_95[$idx]['the_dt'].'\','.$arr_95[$idx]['n_of_trades'].','.$arr_95[$idx]['n_of_trades'].'], ';
		  }
		  
		  ?>
        ]);	

		var options95 = {
          title: 'Trades # Yearly',
		  hAxis: {title: 'Year', titleTextStyle: {color: '#1b7eac'}},          
          legend: { position: 'none' },
		  colors: ['#1b7eac']
        };		
		
		var chart95 = new google.visualization.ColumnChart(document.getElementById('chart_div95'));
        chart95.draw(data95, options95);		
		
		
		
		
		
		
		//// 103 -- Trades Volumes USD weekly
		
        var data103 = google.visualization.arrayToDataTable([
          ['Week', 'Trades Vol USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_93); $idx++)
		  {		  
			echo '[\''.substr($arr_93[$idx]['the_dt'], 3, 2).'\','.$arr_93[$idx]['tr_close_vol_usd'].','.$arr_93[$idx]['tr_close_vol_usd'].'], ';
		  }
		  
		  ?>
        ]);	

		var options103 = {
          title: 'Trades Vol USD Weekly',  
		  hAxis: {title: 'Week', titleTextStyle: {color: '#dd8e07'}}, 		  
          legend: { position: 'none' },
		  colors: ['#dd8e07']		 
        };		
		
		var chart103= new google.visualization.ColumnChart(document.getElementById('chart_div103'));
        chart103.draw(data103, options103);
		
		
		
		//// 104 -- Trades Volumes USD monthly
		
        var data104 = google.visualization.arrayToDataTable([
          ['Month', 'Trades Vol USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_94); $idx++)
		  {		  
			echo '[\''.substr($arr_94[$idx]['the_dt'], 3, 2).'\','.$arr_94[$idx]['tr_close_vol_usd'].','.$arr_94[$idx]['tr_close_vol_usd'].'], ';
		  }
		  
		  ?>
        ]);	

		var options104 = {
          title: 'Trades Vol USD Montly',  
		  hAxis: {title: 'Month', titleTextStyle: {color: '#9dc62d'}}, 
          legend: { position: 'none' },
		  colors: ['#9dc62d']
        };		
		
		var chart104 = new google.visualization.ColumnChart(document.getElementById('chart_div104'));
        chart104.draw(data104, options104);			
		
	
				
		//// 105 -- Trades Vol USD yearly
		
        var data105 = google.visualization.arrayToDataTable([
          ['Year', 'Trades Vol USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_95); $idx++)
		  {		  
			echo '[\''.$arr_95[$idx]['the_dt'].'\','.$arr_95[$idx]['tr_close_vol_usd'].','.$arr_95[$idx]['tr_close_vol_usd'].'], ';
		  }
		  
		  ?>
        ]);	

		var options105 = {
          title: 'Trades Vol USD Yearly',
		  hAxis: {title: 'Year', titleTextStyle: {color: '#1b7eac'}},          
          legend: { position: 'none' },
		  colors: ['#1b7eac']
        };		
		
		var chart105 = new google.visualization.ColumnChart(document.getElementById('chart_div105'));
        chart105.draw(data105, options105);			
		
		






		
		//// 113 -- Trades Profit USD weekly
		
        var data113 = google.visualization.arrayToDataTable([
          ['Week', 'Trades Prof USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_93); $idx++)
		  {		  
			echo '[\''.substr($arr_93[$idx]['the_dt'], 3, 2).'\','.$arr_93[$idx]['tr_close_prof_usd'].','.$arr_93[$idx]['tr_close_prof_usd'].'], ';
		  }
		  
		  ?>
        ]);	

		var options113 = {
          title: 'Trades Prof USD Weekly',  
		  hAxis: {title: 'Week', titleTextStyle: {color: '#dd8e07'}}, 		  
          legend: { position: 'none' },
		  colors: ['#dd8e07']		 
        };		
		
		var chart113= new google.visualization.ColumnChart(document.getElementById('chart_div113'));
        chart113.draw(data113, options113);
		
		
		
		//// 114 -- Trades Profit USD monthly
		
        var data114 = google.visualization.arrayToDataTable([
          ['Month', 'Trades Prof USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_94); $idx++)
		  {		  
			echo '[\''.substr($arr_94[$idx]['the_dt'], 3, 2).'\','.$arr_94[$idx]['tr_close_prof_usd'].','.$arr_94[$idx]['tr_close_prof_usd'].'], ';
		  }
		  
		  ?>
        ]);	

		var options114 = {
          title: 'Trades Prof USD Montly',  
		  hAxis: {title: 'Month', titleTextStyle: {color: '#9dc62d'}}, 
          legend: { position: 'none' },
		  colors: ['#9dc62d']
        };		
		
		var chart114 = new google.visualization.ColumnChart(document.getElementById('chart_div114'));
        chart114.draw(data114, options114);			
		
	
				
		//// 115 -- Trades Profit USD yearly
		
        var data115 = google.visualization.arrayToDataTable([
          ['Year', 'Trades Prof USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_95); $idx++)
		  {		  
			echo '[\''.$arr_95[$idx]['the_dt'].'\','.$arr_95[$idx]['tr_close_prof_usd'].','.$arr_95[$idx]['tr_close_prof_usd'].'], ';
		  }
		  
		  ?>
        ]);	

		var options115 = {
          title: 'Trades Prof USD Yearly',
		  hAxis: {title: 'Year', titleTextStyle: {color: '#1b7eac'}},          
          legend: { position: 'none' },
		  colors: ['#1b7eac']
        };		
		
		var chart115 = new google.visualization.ColumnChart(document.getElementById('chart_div115'));
        chart115.draw(data115, options115);			
		

		
		
		
		//// 123 -- Trades Rev USD weekly
		
        var data123 = google.visualization.arrayToDataTable([
          ['Week', 'Trades Rev USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_93); $idx++)
		  {		  
			echo '[\''.substr($arr_93[$idx]['the_dt'], 3, 2).'\','.$arr_93[$idx]['tr_rev_usd'].','.$arr_93[$idx]['tr_rev_usd'].'], ';
		  }
		  
		  ?>
        ]);	

		var options123 = {
          title: 'Trades Rev USD Weekly',  
		  hAxis: {title: 'Week', titleTextStyle: {color: '#dd8e07'}}, 		  
          legend: { position: 'none' },
		  colors: ['#dd8e07']		 
        };		
		
		var chart123= new google.visualization.ColumnChart(document.getElementById('chart_div123'));
        chart123.draw(data123, options123);
		
		
		
		//// 124 -- Trades Rev USD monthly
		
        var data124 = google.visualization.arrayToDataTable([
          ['Month', 'Trades Rev USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_94); $idx++)
		  {		  
			echo '[\''.substr($arr_94[$idx]['the_dt'], 3, 2).'\','.$arr_94[$idx]['tr_rev_usd'].','.$arr_94[$idx]['tr_rev_usd'].'], ';
		  }
		  
		  ?>
        ]);	

		var options124 = {
          title: 'Trades Rev USD Montly',  
		  hAxis: {title: 'Month', titleTextStyle: {color: '#9dc62d'}}, 
          legend: { position: 'none' },
		  colors: ['#9dc62d']
        };		
		
		var chart124 = new google.visualization.ColumnChart(document.getElementById('chart_div124'));
        chart124.draw(data124, options124);			
		
	
				
		//// 125 -- Trades Rev USD yearly
		
        var data125 = google.visualization.arrayToDataTable([
          ['Year', 'Trades Rev USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_95); $idx++)
		  {		  
			echo '[\''.$arr_95[$idx]['the_dt'].'\','.$arr_95[$idx]['tr_rev_usd'].','.$arr_95[$idx]['tr_rev_usd'].'], ';
		  }
		  
		  ?>
        ]);	

		var options125 = {
          title: 'Trades Rev USD Yearly',
		  hAxis: {title: 'Year', titleTextStyle: {color: '#1b7eac'}},          
          legend: { position: 'none' },
		  colors: ['#1b7eac']
        };		
		
		var chart125 = new google.visualization.ColumnChart(document.getElementById('chart_div125'));
        chart125.draw(data125, options125);			
				
		
		
		
		
		
		/// 100 -- Trades Vol USD Hours
		
        var data100 = google.visualization.arrayToDataTable([
          ['Hour', 'Vol USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_90); $idx++)
		  {		  
			echo '[\''.substr($arr_90[$idx]['the_dt'],9,2).'\','.$arr_90[$idx]['tr_close_vol_usd'].','.$arr_90[$idx]['tr_close_vol_usd'].'], ';
		  }
		  
		  ?>
        ]);	
	   
        var options100 = {
          title: 'Trades Vol USD Hours',
          hAxis: {title: 'Hours',  titleTextStyle: {color: '#e34352'}},
          vAxis: {minValue: 0},
		  colors: ['#e34352']
        };					   

		// Instantiate and draw our chart, passing in some options.
		var chart100 = new google.visualization.AreaChart(document.getElementById('chart_div100'));
		chart100.draw(data100, options100);		
		
		
		
		
		
		/// 101 -- Trades Vol USD Days
		
        var data101 = google.visualization.arrayToDataTable([
          ['Day', 'Vol USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_91); $idx++)
		  {		  
			echo '[\''.$arr_91[$idx]['the_dt'].'\','.$arr_91[$idx]['tr_close_vol_usd'].','.$arr_91[$idx]['tr_close_vol_usd'].'], ';
		  }
		  
		  ?>
        ]);	
	   
        var options101 = {
          title: 'Trades Vol USD Days',
          hAxis: {title: 'Days',  titleTextStyle: {color: '#1b7eac'}},
          vAxis: {minValue: 0},
		  colors: ['#1b7eac']
        };					   

		// Instantiate and draw our chart, passing in some options.
		var chart101 = new google.visualization.AreaChart(document.getElementById('chart_div101'));
		chart101.draw(data101, options101);		
		
		
		
		
		
		
		/// 110 -- Trades Profit USD Hours
		
        var data110 = google.visualization.arrayToDataTable([
          ['Hour', 'Prof USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_90); $idx++)
		  {		  
			echo '[\''.substr($arr_90[$idx]['the_dt'],9,2).'\','.$arr_90[$idx]['tr_close_prof_usd'].','.$arr_90[$idx]['tr_close_prof_usd'].'], ';
		  }
		  
		  ?>
        ]);	
	   
        var options110 = {
          title: 'Trades Prof USD Hours',
          hAxis: {title: 'Hours',  titleTextStyle: {color: '#e34352'}},
          vAxis: {minValue: 0},
		  colors: ['#e34352']
        };					   

		// Instantiate and draw our chart, passing in some options.
		var chart110 = new google.visualization.AreaChart(document.getElementById('chart_div110'));
		chart110.draw(data110, options110);		
		
		
		
		
		
		/// 111 -- Trades Vol USD Days
		
        var data111 = google.visualization.arrayToDataTable([
          ['Day', 'Prof USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_91); $idx++)
		  {		  
			echo '[\''.$arr_91[$idx]['the_dt'].'\','.$arr_91[$idx]['tr_close_prof_usd'].','.$arr_91[$idx]['tr_close_prof_usd'].'], ';
		  }
		  
		  ?>
        ]);	
	   
        var options111 = {
          title: 'Trades Prof USD Days',
          hAxis: {title: 'Days',  titleTextStyle: {color: '#1b7eac'}},
          vAxis: {minValue: 0},
		  colors: ['#1b7eac']
        };					   

		// Instantiate and draw our chart, passing in some options.
		var chart111 = new google.visualization.AreaChart(document.getElementById('chart_div111'));
		chart111.draw(data111, options111);		
				
		
		
		
		
		
		/// 120 -- Trades Revenue USD Hours
		
        var data120 = google.visualization.arrayToDataTable([
          ['Hour', 'Rev USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_90); $idx++)
		  {		  
			echo '[\''.substr($arr_90[$idx]['the_dt'],9,2).'\','.$arr_90[$idx]['tr_rev_usd'].','.$arr_90[$idx]['tr_rev_usd'].'], ';
		  }
		  
		  ?>
        ]);	
	   
        var options120 = {
          title: 'Trades Rev USD Hours',
          hAxis: {title: 'Hours',  titleTextStyle: {color: '#e34352'}},
          vAxis: {minValue: 0},
		  colors: ['#e34352']
        };					   

		// Instantiate and draw our chart, passing in some options.
		var chart120 = new google.visualization.AreaChart(document.getElementById('chart_div120'));
		chart120.draw(data120, options120);		
		
		
		
		
		
		/// 121 -- Trades Rev USD Days
		
        var data121 = google.visualization.arrayToDataTable([
          ['Day', 'Rev USD', {type: 'string', role:'annotation'}],
		  <?php
		  
		  for($idx=0; $idx<sizeof($arr_91); $idx++)
		  {		  
			echo '[\''.$arr_91[$idx]['the_dt'].'\','.$arr_91[$idx]['tr_rev_usd'].','.$arr_91[$idx]['tr_rev_usd'].'], ';
		  }
		  
		  ?>
        ]);	
	   
        var options121 = {
          title: 'Trades Rev USD Days',
          hAxis: {title: 'Days',  titleTextStyle: {color: '#1b7eac'}},
          vAxis: {minValue: 0},
		  colors: ['#1b7eac']
        };					   

		// Instantiate and draw our chart, passing in some options.
		var chart121 = new google.visualization.AreaChart(document.getElementById('chart_div121'));
		chart121.draw(data121, options121);			
		
		
		
		
		
		
		/// 500 - YTD Regs # vs Prev. Year
		
		var data500 = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Regs %', <?php echo $arr_500[0]['pct_change']; ?>]
        ]);
		
		
		var options500 = {
          //width: 400, height: 120,
          redFrom: 0, redTo: 75,
          yellowFrom:75, yellowTo: 100,
		  greenFrom: 100, greenTo: 200,
          minorTicks: 5,
		  max: 200
        };
		
		var chart500 = new google.visualization.Gauge(document.getElementById('chart_div500'));
        chart500.draw(data500, options500);
		
		
		
		/// 501 - YTD FTDs # vs Prev. Year
		
		var data501 = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['FTDs %', <?php echo $arr_501[0]['pct_change']; ?>]
        ]);
		
		
		var options501 = {
          //width: 400, height: 120,
          redFrom: 0, redTo: 75,
          yellowFrom:75, yellowTo: 500,
		  greenFrom: 500, greenTo: 1000,
          minorTicks: 5,
		  max: 1000
        };
		
		var chart501 = new google.visualization.Gauge(document.getElementById('chart_div501'));
        chart501.draw(data501, options501);

		
		
		/// 502 - YTD Deposits Amt EUR # vs Prev. Year
		
		var data502 = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Dep %', <?php echo $arr_502[0]['pct_change']; ?>]
        ]);
		
		
		var options502 = {
          //width: 400, height: 120,
          redFrom: 0, redTo: 75,
          yellowFrom:75, yellowTo: 100,
		  greenFrom: 100, greenTo: 300,
          minorTicks: 5,
		  max: 300
        };
		
		var chart502 = new google.visualization.Gauge(document.getElementById('chart_div502'));
        chart502.draw(data502, options502);		
		
		
		
		
		/// 503 -- YTD Trades Volumes USD # vs Prev. Year (%)
		
		var data503 = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['TVol %', <?php echo $arr_503[0]['pct_change']; ?>]
        ]);
		
		
		var options503 = {
          //width: 400, height: 120,
          redFrom: 0, redTo: 75,
          yellowFrom:75, yellowTo: 400,
		  greenFrom: 400, greenTo: 1000,
          minorTicks: 5,
		  max: 1000
        };
		
		var chart503 = new google.visualization.Gauge(document.getElementById('chart_div503'));
        chart503.draw(data503, options503);	
		
		
		
		
		/// 504 -- YTD Trades Profit USD # vs Prev. Year (%)
		
		var data504 = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['TProf %', <?php echo $arr_504[0]['pct_change']; ?>]
        ]);
		
		
		var options504 = {
          //width: 400, height: 120,
          redFrom: 0, redTo: 75,
          yellowFrom:75, yellowTo: 400,
		  greenFrom: 400, greenTo: 4000,
          minorTicks: 5,
		  max: 4000
        };
		
		var chart504 = new google.visualization.Gauge(document.getElementById('chart_div504'));
        chart504.draw(data504, options504);	

		
		
		
		/// 505 -- YTD Trades Rev USD # vs Prev. Year (%)
		
		var data505 = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['TRev %', <?php echo $arr_505[0]['pct_change']; ?>]
        ]);
		
		
		var options505 = {
          //width: 400, height: 120,
          redFrom: 0, redTo: 75,
          yellowFrom:75, yellowTo: 400,
		  greenFrom: 400, greenTo: 2000,
          minorTicks: 5,
		  max: 2000
        };
		
		var chart505 = new google.visualization.Gauge(document.getElementById('chart_div505'));
        chart505.draw(data505, options505);	
		
		
		
	  }

	</script>  
  
</head>
<body class="application">

  <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Dima | Data Digest | Last Updated: <?php echo $arr_last_upd[0]['the_dt']; ?> (UTC)</a>
      </div>
	  <!--
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-left">
          <li></li>
          <li></li>
          <li></li>
          <li></li>
        </ul>
      </div>
	  -->
    </div>
  </div>

  <div class="container-fluid">
  
    <h4><i class="fa fa-pie-chart">&nbsp;</i>Basic KPIs</h4>
	
	<div class="row">
	
      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            YTD Regs # vs Prev. Year (%)
          </div>
          <div class="chart-stage" >
            <div id="chart_div500" align='center'></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> YTD Regs # <b><?php echo $arr_500[0]['curr_y_regs']; ?></b>; Prev Year Regs # <b><?php echo $arr_500[0]['prev_y_regs']; ?></b>
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            YTD FTDs # vs Prev. Year (%)
          </div>
          <div class="chart-stage">
            <div id="chart_div501" align='center'></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> YTD FTDs # <b><?php echo $arr_501[0]['curr_y_ftds']; ?></b>; Prev. Year FTDs # <b><?php echo $arr_501[0]['prev_y_ftds']; ?></b>
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            YTD Deposits Amt EUR vs Prev. Year (%)
          </div>
          <div class="chart-stage">
            <div id="chart_div502" align='center'></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> YTD DepEUR <b><?php echo $arr_502[0]['curr_y_depeur']; ?></b>; Prev. YDepEUR <b><?php echo $arr_502[0]['prev_y_depeur']; ?></b>
          </div>
        </div>
      </div>	  
	
	
	</div>
	
	
	<div class="row">
	
      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            YTD Trades Vol USD # vs Prev. Year (%)
          </div>
          <div class="chart-stage">
            <div id="chart_div503" align='center'></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> YTD TVolUSD <b><?php echo $arr_503[0]['curr_y_vol_usd']; ?></b>; Prev. Year <b><?php echo $arr_503[0]['prev_y_vol_usd']; ?></b>
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            YTD Trades Profit USD vs Prev. Year (%)
          </div>
          <div class="chart-stage">
            <div id="chart_div504" align='center'></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> YTD Trades Profit USD <b><?php echo $arr_504[0]['curr_y_prof_usd']; ?></b>; Prev. Year <b><?php echo $arr_504[0]['prev_y_prof_usd']; ?></b>
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            YTD Trades Rev USD vs Prev. Year (%)
          </div>
          <div class="chart-stage">
            <div id="chart_div505" align='center'></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> YTD Trades Rev USD <b><?php echo $arr_505[0]['curr_y_rev_usd']; ?></b>; Prev. Year <b><?php echo $arr_505[0]['prev_y_rev_usd']; ?></b>
          </div>
        </div>
      </div>	  
	
	
	</div>
	
  
	<h4><i class="fa fa-users">&nbsp;</i>Online Users</h4>
  
    <div class="row">
	
      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Online Users # recent Hours
          </div>
          <div class="chart-stage">
            <div id="chart_div80"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Hours
          </div>
        </div>
      </div>
	 
	</div>
	
	<div class="row">
 
      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Online Users # recent Days
          </div>
          <div class="chart-stage">
            <div id="chart_div81"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Days
          </div>
        </div>
      </div>

    </div>  


    <div class="row">	  

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Online Users # recent Weeks
          </div>
          <div class="chart-stage">
            <div id="chart_div83"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Weeks
          </div>
        </div>
      </div>
	  
      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Online Users # recent Months
          </div>
          <div class="chart-stage">
            <div id="chart_div84"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Months
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Online Users # recent Years
          </div>
          <div class="chart-stage">
            <div id="chart_div85"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Years
          </div>
        </div>
      </div>	  

    </div>  	
  
    <h4><i class="fa fa-chevron-down">&nbsp;</i>Registrations</h4>
  
    <div class="row">

      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Registrations # recent Hours
          </div>
          <div class="chart-stage">
            <div id="chart_div0"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Hours
          </div>
        </div>
      </div>

    </div>  
  
  
    <div class="row">

      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Registrations # recent Days
          </div>
          <div class="chart-stage">
            <div id="chart_div"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Days
          </div>
        </div>
      </div>

    </div>


    <div class="row">
	  
      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Registrations # Weekly
          </div>
          <div class="chart-stage">
            <div id="chart_div4"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Weeks
          </div>
        </div>
      </div>	  
	  
      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Registrations # Monthly
          </div>
          <div class="chart-stage">
            <div id="chart_div3"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Months
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Registrations # Yearly
          </div>
          <div class="chart-stage">
            <div id="chart_div2"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Years
          </div>
        </div>
      </div>	  

	</div>	
	
   <h4><i class="fa fa-map">&nbsp;</i>Countries</h4>
	
   <div class="row">

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Top Countries Regs # Last Week
          </div>
          <div class="chart-stage">
            <div id="chart_div7"></div>
          </div>
          <div class="chart-notes">
            Top Countries Regs # Last Week
          </div>
        </div>
      </div>	  
	  
      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Top Countries Regs # Last Month
          </div>
          <div class="chart-stage">
            <div id="chart_div6"></div>
          </div>
          <div class="chart-notes">
            Top Countries Regs # Last Month
          </div>
        </div>
      </div>
	  

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Top Countries Regs # Last Year
          </div>
          <div class="chart-stage">
            <div id="chart_div5"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Top Countries Regs # Last Year
          </div>
        </div>
      </div>	  

	</div>	



    <div class="row">

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Top Countries FTDs # Last Week
          </div>
          <div class="chart-stage">
            <div id="chart_div61"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Top Countries FTDs # Last Week
          </div>
        </div>
      </div>

 

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Top Countries FTDs # Last Month
          </div>
          <div class="chart-stage">
            <div id="chart_div62"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Top Countries FTDs # Last Month
          </div>
        </div>
      </div>


      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Top Countries FTDs # Last Year
          </div>
          <div class="chart-stage">
            <div id="chart_div63"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Top Countries FTDs # Last Year
          </div>
        </div>
      </div>

    </div>		
	
	
	<h4><i class="fa fa-credit-card">&nbsp;</i>FTDs</h4>
	
    <div class="row">

      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            FTDs # recent Days
          </div>
          <div class="chart-stage">
            <div id="chart_div10"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Days
          </div>
        </div>
      </div>

    </div>  	
	
	
	
    <div class="row">
	  
      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            FTDs # Weekly
          </div>
          <div class="chart-stage">
            <div id="chart_div30"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Weeks
          </div>
        </div>
      </div>	  
	  
      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            FTDs # Monthly
          </div>
          <div class="chart-stage">
            <div id="chart_div31"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Months
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            FTDs # Yearly
          </div>
          <div class="chart-stage">
            <div id="chart_div32"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Years
          </div>
        </div>
      </div>	  

	</div>		
	
	
	<h4><i class="fa fa-money">&nbsp;</i>Deposits</h4>
	
    <div class="row">

      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Deposits recent Days
          </div>
          <div class="chart-stage">
            <div id="chart_div41"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Deposits recent Days
          </div>
        </div>
      </div>

	</div> 	  
	  
	<div class="row"> 
	  
      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Deposits recent Weeks
          </div>
          <div class="chart-stage">
            <div id="chart_div42"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Deposits recent Weeks
          </div>
        </div>
      </div>

    </div> 		
	
	
	
    <div class="row">

      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Deposits recent Months
          </div>
          <div class="chart-stage">
            <div id="chart_div43"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Deposits recent Months
          </div>
        </div>
      </div>
	 
	</div>
	
	<div class="row">

      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Deposits recent Years
          </div>
          <div class="chart-stage">
            <div id="chart_div44"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Deposits recent Years
          </div>
        </div>
      </div>

    </div>


	<h4><i class="fa fa-signal">&nbsp;</i>Trades</h4>
	
	
	<!--- num of trades --->
	
    <div class="row">
	
      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Trades # recent Hours
          </div>
          <div class="chart-stage">
            <div id="chart_div90"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Hours
          </div>
        </div>
      </div>
	 
	</div>
	
	<div class="row">
 
      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Trades # recent Days
          </div>
          <div class="chart-stage">
            <div id="chart_div91"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Days
          </div>
        </div>
      </div>

    </div>  


    <div class="row">	  

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Trades # recent Weeks
          </div>
          <div class="chart-stage">
            <div id="chart_div93"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Weeks
          </div>
        </div>
      </div>
	  
      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Trades # recent Months
          </div>
          <div class="chart-stage">
            <div id="chart_div94"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Months
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Trades # recent Years
          </div>
          <div class="chart-stage">
            <div id="chart_div95"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Years
          </div>
        </div>
      </div>	  

    </div>  		
	
	
	<!--- closed volume usd --->
	
	
    <div class="row">
	
      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Trades Volume USD recent Hours
          </div>
          <div class="chart-stage">
            <div id="chart_div100"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Hours
          </div>
        </div>
      </div>
	 
	</div>
	
	<div class="row">
 
      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Trades Volume USD recent Days
          </div>
          <div class="chart-stage">
            <div id="chart_div101"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Days
          </div>
        </div>
      </div>

    </div>  


    <div class="row">	  

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Trades Volume USD recent Weeks
          </div>
          <div class="chart-stage">
            <div id="chart_div103"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Weeks
          </div>
        </div>
      </div>
	  
      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Trades Volume USD recent Months
          </div>
          <div class="chart-stage">
            <div id="chart_div104"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Months
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Trades Volume USD recent Years
          </div>
          <div class="chart-stage">
            <div id="chart_div105"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Years
          </div>
        </div>
      </div>	  

    </div>  		
	
	
	<!---  profit usd ---->
	
    <div class="row">
	
      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Profit USD recent Hours
          </div>
          <div class="chart-stage">
            <div id="chart_div110"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Hours
          </div>
        </div>
      </div>
	 
	</div>
	
	<div class="row">
 
      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Profit USD recent Days
          </div>
          <div class="chart-stage">
            <div id="chart_div111"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Days
          </div>
        </div>
      </div>

    </div>  


    <div class="row">	  

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Profit USD recent Weeks
          </div>
          <div class="chart-stage">
            <div id="chart_div113"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Weeks
          </div>
        </div>
      </div>
	  
      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Profit USD recent Months
          </div>
          <div class="chart-stage">
            <div id="chart_div114"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Months
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Profit USD recent Years
          </div>
          <div class="chart-stage">
            <div id="chart_div115"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Years
          </div>
        </div>
      </div>	  

    </div>  			
	
	
	<!---- revenue usd ---->
	
   <div class="row">
	
      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Revenue USD recent Hours
          </div>
          <div class="chart-stage">
            <div id="chart_div120"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Hours
          </div>
        </div>
      </div>
	 
	</div>
	
	<div class="row">
 
      <div class="col-sm-12">
        <div class="chart-wrapper">
          <div class="chart-title">
            Revenue USD recent Days
          </div>
          <div class="chart-stage">
            <div id="chart_div121"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Days
          </div>
        </div>
      </div>

    </div>  


    <div class="row">	  

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Revenue USD recent Weeks
          </div>
          <div class="chart-stage">
            <div id="chart_div123"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Weeks
          </div>
        </div>
      </div>
	  
      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Revenue USD recent Months
          </div>
          <div class="chart-stage">
            <div id="chart_div124"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Months
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Revenue USD recent Years
          </div>
          <div class="chart-stage">
            <div id="chart_div125"></div>
          </div>
          <div class="chart-notes">
            <i class="fa fa-clock-o"></i> Recent Years
          </div>
        </div>
      </div>	  

    </div>  			
	
	

    <hr>

    <p class="small text-muted">Built by Dmitry</p>

  </div>

  <script type="text/javascript" src="assets/lib/holderjs/holder.js"></script>
  <script>
    Holder.add_theme("white", { background:"#fff", foreground:"#a7a7a7", size:10 });
  </script>

</body>
</html>
