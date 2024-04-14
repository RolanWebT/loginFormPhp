<?php 
#**********************************************************************************#


				#********************************#
				#*********** DATE INC ***********#
				#********************************#


#**********************************************************************************#
				/**
				*
				*	Calculate the diffirence between tow Dates in year(s), month(s), day(s)
				*
				*	@param DateTime Object	$old_date	 
				*	@param DateTime Object	$new_date	
				*
				*	@return String 				- The difference between the given tow Dates formated 
				*
				*/

				function calc_date_diff($old_date, $new_date) {
if(DEBUG_F)		echo "<p class='debug dateTime'>🌀 <b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "() <i>(" . basename(__FILE__) . ")</i></p>\n";						

					if (date_diff($old_date, $new_date)->format('%y') > 0) {
						if (date_diff($old_date, $new_date)->format('%y') == 1) {

							if (date_diff($old_date, $new_date)->format('%m') > 0) {
								if (date_diff($old_date, $new_date)->format('%m') == 1) {

									if (date_diff($old_date, $new_date)->format('%d') > 0) {
										if (date_diff($old_date, $new_date)->format('%d') == 1) {
											// Year, Month, Day
											return date_diff($old_date, $new_date)->format('%y Year, %m Month and %d day');
										} else {
											// Year, Month, Days
											return date_diff($old_date, $new_date)->format('%y Year, %m Month and %d days');
										}

									} else {
										// Year, Month, 0
										return date_diff($old_date, $new_date)->format('%y Year and %m Month');
									}

								} else {
									// %m > 1
									if (date_diff($old_date, $new_date)->format('%d') > 0) {
										if (date_diff($old_date, $new_date)->format('%d') == 1) {
											// Year, Months, Day
											return date_diff($old_date, $new_date)->format('%y Year, %m Months and %d day');
										} else {
											// Year, Months, Days
											return date_diff($old_date, $new_date)->format('%y Year, %m Months and %d days');
										}

									} else {
										// Year, Months, 0
										return date_diff($old_date, $new_date)->format('%y Year and %m Months');
									}
								}

							} else {
								// %m = 0

								if (date_diff($old_date, $new_date)->format('%d') > 0) {
										if (date_diff($old_date, $new_date)->format('%d') == 1) {
											// Year, 0, Day
											return date_diff($old_date, $new_date)->format('%y Year and %d day');
										} else {
											// Year, 0, Days
											return date_diff($old_date, $new_date)->format('%y Year and %d days');
										}

									} else {
										// Year, 0, 0
										return date_diff($old_date, $new_date)->format('%y Year');
									}
								
							}

						} else {
							// %y > 1
							if (date_diff($old_date, $new_date)->format('%m') > 0) {
								if (date_diff($old_date, $new_date)->format('%m') == 1) {

									if (date_diff($old_date, $new_date)->format('%d') > 0) {
										if (date_diff($old_date, $new_date)->format('%d') == 1) {
											// Years, Month, Day
											return date_diff($old_date, $new_date)->format('%y Years, %m Month and %d day');
										} else {
											// Years, Month, Days
											return date_diff($old_date, $new_date)->format('%y Years, %m Month and %d days');
										}

									} else {
										// Years, Monthe
										return date_diff($old_date, $new_date)->format('%y Years and %m Month');
									}

								} else {
									// %m > 1
									if (date_diff($old_date, $new_date)->format('%d') > 0) {
										if (date_diff($old_date, $new_date)->format('%d') == 1) {
											// Years, Months, Day
											return date_diff($old_date, $new_date)->format('%y Years, %m Months and %d day');
										} else {
											// Years, Months, Days
											return date_diff($old_date, $new_date)->format('%y Years, %m Months and %d days');
										}

									} else {
										// Years, Months
										return date_diff($old_date, $new_date)->format('%y Years and %m Months');
									}
								}

							} else {
								// %m = 0

								if (date_diff($old_date, $new_date)->format('%d') > 0) {
										if (date_diff($old_date, $new_date)->format('%d') == 1) {
											return date_diff($old_date, $new_date)->format('%y Years and %d day');
										} else {
											// Years, 0, days
											return date_diff($old_date, $new_date)->format('%y Years and %d days');
										}

									} else {
										// Years, 0, 0
										return date_diff($old_date, $new_date)->format('%y Years');
									}
								
							}
						}
					} else {
						if (date_diff($old_date, $new_date)->format('%m') > 0) {
								if (date_diff($old_date, $new_date)->format('%m') == 1) {

									if (date_diff($old_date, $new_date)->format('%d') > 0) {
										if (date_diff($old_date, $new_date)->format('%d') == 1) {
											// 0, Month, Day
											return date_diff($old_date, $new_date)->format('%m Month and %d day');
										} else {
											// 0, Month, Days
											return date_diff($old_date, $new_date)->format('%m Month and %d days');
										}

									} else {
										// 0, Month, 0
										return date_diff($old_date, $new_date)->format('%m Month');
									}

								} else {
									// %m > 1
									if (date_diff($old_date, $new_date)->format('%d') > 0) {
										if (date_diff($old_date, $new_date)->format('%d') == 1) {
											// 0, Months, Day
											return date_diff($old_date, $new_date)->format('%m Months and %d day');
										} else {
											// 0, Months, Days
											return date_diff($old_date, $new_date)->format('%m Months and %d days');
										}

									} else {
										// 0, Months, 0
										return date_diff($old_date, $new_date)->format('%%m Months');
									}
								}

							} else {
								// %m = 0

								if (date_diff($old_date, $new_date)->format('%d') > 0) {
										if (date_diff($old_date, $new_date)->format('%d') == 1) {
											// 0, 0, Day
											return date_diff($old_date, $new_date)->format('%d day');
										} else {
											// 0, 0, Days
											return date_diff($old_date, $new_date)->format('%d days');
										}

									} else {
										// 0, 0, 0
										return 'today at ' . $old_date->format('H:i:s');
									}
								
							}
					}
				}

#**********************************************************************************#
?>                