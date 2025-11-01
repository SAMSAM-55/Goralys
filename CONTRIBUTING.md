# Contributing to Goralys

Thank you for your interest in contributing to Goralys! This document provides guidelines for contributing to the project.

## Code of Conduct

Please note that this project is released with a Contributor Code of Conduct. By participating in this project you agree to abide by its terms.

## How to Contribute

### Pull Requests

1. Fork the repository and create your branch from `dev`
2. Install dependencies: `composer install --working-dir=PHP`
3. Make your changes following the coding conventions below
4. Write clear commit messages
5. Update documentation as needed
6. Submit a pull request to the `dev` branch

*Note: Never create a pull request to the `main` branch*

#### Coding Conventions

- Follow PSR-12 coding style for PHP code
- Use 4 spaces for indentation (no tabs)
- Add comments for complex logic
- Always write the meaning of the HTTP codes in PHP
- Write descriptive variable/function names
- Write code in English

#### Security Guidelines

- Never commit sensitive credentials (use `PHP/config_secret.php` for local testing)
- Use prepared statements for database queries
- Implement CSRF protection for forms

### File Organization

New code should follow the existing project structure:

```
goralys/
├── CSS/                   # Stylesheets
├── JS/                    # Frontend JavaScript
├── PHP/                   # Backend PHP files
│   ├── subject/           # Subject-related endpoints
│   ├── vendor/            # Composer dependencies
│   ├── config.php         # Configuration (no credentials)
│   └── utility.php        # Helper functions
└── *_page.php             # Pages that require PHP
└── *.html                 # Static pages that don't require PHP
```

### Documentation

- Update README.md for significant changes
- Keep code comments current
- Update database schema in `PHP/data_structure.txt` if needed; if you do so, please provide a test file (.sql)

### Testing

Before submitting a PR:

1. Test your changes locally
2. Verify CSRF protection works
3. Check form validation
4. Test database operations
5. Verify frontend interactions
6. Test with different user roles (student/teacher/admin)

### Development Environment

Recommended setup:

- PHP 7.4+
- MySQL/MariaDB
- Apache with mod_rewrite
- Composer
- IDE with PHP support (VS Code, PhpStorm, etc.)

*Note: I personally recommend PhpStorm if you can afford it as it integrates seamlessly with a local server and database*

### Getting Help

If you need help:

1. Check the [README.md](README.md)
2. Look through existing issues
3. Open a new issue with your question

## License

By contributing, you agree that your contributions will be licensed under the project's [MIT License](LICENSE).

## Acknowledgements

Thanks to all contributors who help improve Goralys!