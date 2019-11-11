<?php


namespace Hypocenter\LaravelSignature\Interfaces;


interface Resolver extends SetConfig
{
    public function getAppId(): ?string;

    public function getSign(): ?string;

    public function getTimestamp(): ?string;

    public function getNonce(): ?string;

    public function getMethod(): ?string;

    public function getPath(): ?string;

    public function getData(): ?array;
}