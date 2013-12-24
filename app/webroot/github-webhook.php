<?php 
try
{
  $payload = json_decode($_REQUEST['payload']);
}
catch(Exception $e)
{
  exit(0);
}

//log the request
file_put_contents('github.txt', print_r($payload, TRUE), FILE_APPEND);


if ($payload->ref === 'refs/heads/master')
{
  // path to your site deployment script
  exec('../../git-pull.sh', $output);
	file_put_contents('github.txt', print_r($output, true), FILE_APPEND);
}
