<?php
function mediaTypeIsImage($media_item): bool
{
    return $media_item["type"] === 'image';
}
