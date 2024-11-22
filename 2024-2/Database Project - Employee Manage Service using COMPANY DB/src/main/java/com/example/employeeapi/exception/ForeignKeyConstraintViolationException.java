package com.example.employeeapi.exception;

public class ForeignKeyConstraintViolationException extends ConstraintViolationException {
    public ForeignKeyConstraintViolationException(String message) {
        super(message);
    }
}
