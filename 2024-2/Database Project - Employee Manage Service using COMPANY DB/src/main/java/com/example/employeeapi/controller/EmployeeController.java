package com.example.employeeapi.controller;

import com.example.employeeapi.exception.ConstraintViolationException;
import com.example.employeeapi.model.Employee;
import com.example.employeeapi.service.EmployeeService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

@Tag(name = "EMPLOYEE", description = "Employee management APIs")
@RestController
@RequestMapping("/api/employee")
public class EmployeeController {

    private final EmployeeService employeeService;

    @Autowired
    public EmployeeController(EmployeeService employeeService) {
        this.employeeService = employeeService;
    }

    @ExceptionHandler(ConstraintViolationException.class)
    public ResponseEntity<String> handleConstraintViolationException(ConstraintViolationException e) {
        return ResponseEntity.badRequest().body(e.getMessage());
    }

    @Operation(summary = "모든 직원 조회", description = "모든 직원 정보를 조회합니다.", tags = {"조회(GET)"})
    @GetMapping
    public ResponseEntity<List<Employee>> getAllEmployees() {
        List<Employee> employees = employeeService.getAllEmployees();
        return ResponseEntity.ok(employees);
    }

    @Operation(summary = "휴지통 직원 조회", description = "휴지통의 모든 직원 정보를 조회합니다.", tags = {"조회(GET)"})
    @GetMapping("/trash")
    public ResponseEntity<List<Employee>> getAllTrashes() {
        List<Employee> employees = employeeService.getAllTrashes();
        return ResponseEntity.ok(employees);
    }

    @Operation(summary = "특정 ssn 직원 조회", description = "ssn값을 통해 특정 직원을 조회합니다.", tags = {"조회(GET)"})
    @GetMapping("/{employee_ssn}")
    public ResponseEntity<Employee> getEmployeeBySsn(@PathVariable("employee_ssn") String employeeSsn) {
        Employee employee = employeeService.getEmployeeBySsn(employeeSsn);
        return employee != null ? ResponseEntity.ok(employee) : ResponseEntity.notFound().build();
    }

    @Operation(summary = "직원 검색", description = "임의의 특성값을 통해 직원을 조회합니다.", tags = {"조회(GET)"})
    @GetMapping("/search")
    public ResponseEntity<?> getEmployeeByAttr(
            @RequestParam(value = "search_attr") List<String> searchAttr,
            @RequestParam(value = "employee_value") List<String> employeeValue) {

        if (searchAttr.size() != employeeValue.size()) {
            return ResponseEntity.badRequest().body("not matching parameters");
        }

        List<Object> searchValue = new ArrayList<>();
        for (int i = 0; i < searchAttr.size(); i++) {
            String attr = searchAttr.get(i);
            String value = employeeValue.get(i);
            try {
                switch (attr.toLowerCase()) {
                    case "fname", "minit", "lname", "ssn", "bdate", "address", "sex", "super_ssn", "dname" -> searchValue.add(value);
                    case "salary" -> searchValue.add(Double.parseDouble(value));
                    case "trash" -> searchAttr.remove(i--);
                    default -> {
                        return ResponseEntity.badRequest().body("Unknown attribute: " + attr);
                    }
                }
            } catch (NumberFormatException e) {
                return ResponseEntity.badRequest().body("Invalid numeric value for attribute: " + attr);
            } catch (Exception e) {
                return ResponseEntity.badRequest().body("Invalid value for attribute: " + attr);
            }
        }

        List<Employee> employees = employeeService.getEmployeeByAttr(searchAttr, searchValue);
        return employees != null ? ResponseEntity.ok(employees) : ResponseEntity.notFound().build();
    }

    @Operation(summary = "직원 삭제(휴지통)", description = "해당하는 ssn값의 직원 정보를 휴지통으로 삭제합니다.", tags = {"삭제(DELETE)"})
    @DeleteMapping("/{employee_ssn}")
    public ResponseEntity<String> soft_deleteEmployeeBySsn(@PathVariable("employee_ssn") String employeeSsn) {
        boolean isDeleted = employeeService.soft_deleteEmployeeBySsn(employeeSsn);
        return isDeleted ? ResponseEntity.ok("Employee deleted successfully") : ResponseEntity.notFound().build();
    }

    @Operation(summary = "직원 완전 삭제", description = "해당하는 ssn값의 직원 정보를 완전히 삭제합니다.", tags = {"삭제(DELETE)"})
    @DeleteMapping("/hard/{employee_ssn}")
    public ResponseEntity<String> hard_deleteEmployeeBySsn(@PathVariable("employee_ssn") String employeeSsn) {
        boolean isDeleted = employeeService.hard_deleteEmployeeBySsn(employeeSsn);
        return isDeleted ? ResponseEntity.ok("Employee deleted successfully") : ResponseEntity.notFound().build();
    }

    @Operation(summary = "직원 복원", description = "휴지통에 있는 직원 정보를 복원합니다.", tags = {"업데이트(PUT)"})
    @PutMapping("/restore/{employee_ssn}")
    public ResponseEntity<String> restoreEmployeeBySsn(@PathVariable("employee_ssn") String employeeSsn) {
        boolean isRestored = employeeService.restoreEmployeeBySsn(employeeSsn);
        return isRestored ? ResponseEntity.ok("Employee restored successfully") : ResponseEntity.notFound().build();
    }

    @Operation(summary = "직원 정보 수정", description = "특정 ssn값의 직원 정보를 수정합니다.", tags = {"업데이트(PUT)"})
    @PutMapping("/{employee_ssn}")
    public ResponseEntity<Employee> updateEmployeeBySsn(
            @PathVariable("employee_ssn") String employeeSsn,
            @RequestBody Map<String, Object> changeValue) {
        Employee updatedEmployee = employeeService.updateEmployeeBySsn(employeeSsn, changeValue);
        return updatedEmployee != null ? ResponseEntity.ok(updatedEmployee) : ResponseEntity.notFound().build();
    }

    @Operation(summary = "직원 정보 추가", description = "해당하는 데이터의 직원을 새로 추가합니다.", tags = {"추가(POST)"})
    @PostMapping
    public ResponseEntity<Employee> addEmployee(@RequestBody List<Object> addingValue) {
        Employee createdEmployee = employeeService.addEmployee(addingValue);
        return ResponseEntity.status(201).body(createdEmployee);
    }

    @Operation(summary = "그룹별 통계 조회", description = "선택된 그룹 기준(성별, 부서, 상급자)으로 평균, 최대, 최소 월급을 조회합니다.",tags = {"조회(GET)"})
    @GetMapping("/group-info/{groupBy}")
    public ResponseEntity<List<Map<String, Object>>> getGroupInfo(@PathVariable("groupBy") String groupBy) {
        try {
            List<Map<String, Object>> groupAverage = employeeService.getGroupInfo(groupBy);
            return ResponseEntity.ok(groupAverage);
        } catch (IllegalArgumentException e) {
            return ResponseEntity.badRequest().body(null); // 잘못된 groupBy 값 처리
        } catch (Exception e) {
            return ResponseEntity.status(500).body(null); // 기타 오류 처리
        }
    }
}