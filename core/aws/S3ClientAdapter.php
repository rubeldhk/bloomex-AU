<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/core/aws/aws-autoloader.php';
use Aws\Exception\AwsException;
use Aws\S3\S3Client;

class S3ClientAdapter
{
    private $s3;
    private $bucket;

    public function __construct()
    {

    global $mosConfig_aws_s3_access_key,$mosConfig_aws_s3_secret,$mosConfig_aws_s3_bucket,$mosConfig_aws_s3_region;


        $this->bucket = $mosConfig_aws_s3_bucket;

        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => $mosConfig_aws_s3_region,
            'credentials' => [
                'key' => $mosConfig_aws_s3_access_key,
                'secret' => $mosConfig_aws_s3_secret,
            ],
        ]);
    }

    /**
     * Загрузка файла в S3
     */
    public function upload($localFilePath, $remoteFileName, $path,$public = true)
    {
        try {
            $result = $this->s3->putObject([
                'Bucket' => $this->bucket,
                'Key' => $path ? $path.'/'.$remoteFileName : $remoteFileName,
                'SourceFile' => $localFilePath,
            ]);

            return $result['ObjectURL'];
        } catch (AwsException $e) {
            return false;
        }
    }

    /**
     * Получить URL файла (публичного или временного)
     */
    public function getUrl($remoteFileName, $expiresMinutes = 10)
    {
        try {
            $cmd = $this->s3->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key' => $remoteFileName,
            ]);

            $request = $this->s3->createPresignedRequest($cmd, '+' . $expiresMinutes . ' minutes');

            return (string)$request->getUri();
        } catch (AwsException $e) {
            return false;
        }
    }
    private function getFileNameFromPublicUrl($privateUrl)
    {
        return trim(parse_url(urldecode($privateUrl), PHP_URL_PATH),'/');
    }
    /**
     * Удалить файл из S3
     */
    public function delete($remoteFileName)
    {
        $remoteFileName = $this->getFileNameFromPublicUrl($remoteFileName);
        try {
            $this->s3->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $remoteFileName,
            ]);
            return true;
        } catch (AwsException $e) {
            return false;
        }
    }
}
