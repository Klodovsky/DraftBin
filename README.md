
<!-- PROJECT DASHBOARD -->
  <h3 align="center">DraftBin</h3>
  <p align="center">
    DraftBin is a website inspired by PasteBin where you can store code/text online for a set period of time and share to anybody around the world.
    <br /> 
![alt text](https://github.com/Klodovsky/DraftBin/blob/master/resources/assets/img/DraftBin_home.PNG)
    <a href="https://github.com/othneildrew/Best-README-Template"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <a href="https://github.com/othneildrew/Best-README-Template">View Demo</a>
    ·
    <a href="https://github.com/Klodovsky/DraftBin/issues">Report Bug</a>
    ·
    <a href="https://github.com/Klodovsky/DraftBin/issues">Request Feature</a>
  </p>
</p>



<!-- TABLE OF CONTENTS -->
<details open="open">
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
          <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgements">Acknowledgements</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

  ![alt text](https://github.com/Klodovsky/DraftBin/blob/master/resources/assets/img/dash.PNG)

## Features :
* Privacy options
* Expiration options
* Destroy after reading
* Password protection (server-side hashed)
* User dashboard
* Raw paste viewing
* Captcha protection (antibots)

### Built With

* [Laravel](https://laravel.com)
* [HTML](https://getbootstrap.com)
* [CSS](https://jquery.com)



<!-- GETTING STARTED -->
## Getting Started

First of all, you need to install php, composer and a database server.

### Installation

1. Clone the repo
   ```sh
   git clone https://github.com/Klodovsky/DraftBin.git
   ```
2. Install Composer
  ```sh
  php composer install
   ```
3. Rename .env.example to .env and fill it with your database details then run :
   ```sh
   php artisan key:generate
   ```
4. Run migrations
 ```sh
   php artisan migrate
   ```
5. Start the developement server :
   ```sh
   php artisan serve
      ```
<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to be learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request



<!-- LICENSE -->
## License

Distributed under the MIT License. See `LICENSE` for more information.



<!-- CONTACT -->
## Contact

Your Name - [@Klodovsky](https://twitter.com/Klodovsky) - khaled.benhassen[at]polytechnicien.tn

Project Link: [https://github.com/Klodovsky/DraftBin](https://github.com/Klodovsky/DraftBin)



<!-- ACKNOWLEDGEMENTS -->
## Acknowledgements
* [GitHub Emoji Cheat Sheet](https://www.webpagefx.com/tools/emoji-cheat-sheet)
* [Img Shields](https://shields.io)
* [Choose an Open Source License](https://choosealicense.com)
* [Best-README-Template](https://github.com/othneildrew/Best-README-Template)

## TO-DO
* Update captcha to v3
