<?php 
class regular
{
	const EMAIL = '/^[A-Za-z0-9\._]+@[A-Za-z0-9]+[A-Za-z0-9\-\.]*\.[A-Za-z]{2,4}$/';
	const PHONE = '/^(\+|0)+\d{9,10}$/';
	const NOTNULL = '/^.*[a-z0-9]+.*$/i';
}