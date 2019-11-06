<?php


namespace Hypocenter\LaravelSignature;


class Define
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $secret;
    /**
     * @var null|array
     */
    private $config;

    public function __construct($id, $name, $secret, $config)
    {
        $this->id = $id;
        $this->name = $name;
        $this->secret = $secret;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @return array|null
     */
    public function getConfig(): ?array
    {
        return $this->config;
    }

}