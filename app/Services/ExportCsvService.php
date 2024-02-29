<?php

namespace App\Services;

use ZipArchive;

class ExportCsvService
{
    public function exportMultipleFiles($posts, $comments, $likes)
    {

        $fileNames =[];
        // Create a temporary directory to store the individual CSV files
        $tempDir = sys_get_temp_dir() . '/csv_new_export';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $this->createUserInfoData($tempDir, $fileNames);

        $this->createPostData($tempDir, $fileNames, $posts);

        $this->createCommentData($tempDir, $fileNames, $comments);

        $this->createLikeData($tempDir, $fileNames, $likes);

        return $this->createZip($tempDir, $fileNames);
    }

    private function createData(&$tempDir, &$fileNames, $name, $header, $model, $data)
    {
        $csvFileName = $tempDir . '/' . $name . auth()->id() . '.csv';
        $fileNames[] = $csvFileName;
        $handle = fopen($csvFileName, 'w');

        fputcsv($handle, $header);
        if(!$model) {
            fputcsv($handle, $data);
        } else {
            foreach ($model as $item) {
                $rowData = [];
                foreach ($data as $attribute) {
                    $rowData[] = $item[$attribute];
                }
                fputcsv($handle, $rowData);
            }
        }
        fclose($handle);
    }

    private function createUserInfoData(&$tempDir, &$fileNames)
    {
        $name = 'user_';
        $header = ['ID', 'Name', 'Email'];
        $model = null;
        $data = [
            auth()->id(),
            auth()->user()->name,
            auth()->user()->email,
        ];

        $this->createData($tempDir, $fileNames, $name, $header, $model, $data);
    }

    private function createPostData(&$tempDir, &$fileNames, $posts)
    {
        $name = 'posts_';
        $header = ['ID', 'User_id', 'Content', 'Image_name'];
        $model = $posts;
        $data = ['id', 'user_id', 'content', 'image_name'];

        $this->createData($tempDir, $fileNames, $name, $header, $model, $data);
    }

    private function createCommentData(&$tempDir, &$fileNames, $comments)
    {
        $name = 'comments_';
        $header = ['ID', 'User_id', 'Post_id', 'Content'];
        $model = $comments;
        $data = ['id', 'user_id', 'post_id', 'content'];

        $this->createData($tempDir, $fileNames, $name, $header, $model, $data);
    }

    private function createLikeData(&$tempDir, &$fileNames, $likes)
    {
        $name = 'likes_';
        $header = ['ID', 'User_id', 'Post_id'];
        $model = $likes;
        $data = ['id', 'user_id', 'post_id'];

        $this->createData($tempDir, $fileNames, $name, $header, $model, $data);
    }

    private function createZip(&$tempDir, &$fileNames)
    {
        // Create a zip archive containing all CSV files
        $zipFileName = 'user_'. auth()->id() .'_export.zip';
        $zipFilePath = $tempDir . '/' . $zipFileName;
        $zip = new ZipArchive();
        $zip->open($zipFilePath, ZipArchive::CREATE);

        $csvFiles = glob($tempDir . '/*.csv');

        foreach ($csvFiles as $csvFile) {
            $zip->addFile($csvFile, basename($csvFile));
        }

        $zip->close();

        // Set headers for the response
        $headers = array(
            "Content-type"        => "application/zip",
            "Content-Disposition" => "attachment; filename=$zipFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $csvFiles = glob($tempDir . '/*.csv');

        foreach ($csvFiles as $csvFile) {
            if(in_array($csvFile, $fileNames)) {
                unlink($csvFile);
            }
        }

        // Return the zip file as a downloadable response
        return response()->download($zipFilePath, $zipFileName, $headers)
            ->deleteFileAfterSend(true);
    }
}
