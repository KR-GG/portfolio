package com.example.employeeapi.exception;

public class UniqueConstraintViolationException extends ConstraintViolationException {
    public UniqueConstraintViolationException(String message) {
        super(message);
    }
}