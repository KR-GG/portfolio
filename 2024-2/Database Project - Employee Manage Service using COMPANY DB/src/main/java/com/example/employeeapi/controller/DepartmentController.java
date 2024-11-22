package com.example.employeeapi.controller;

import com.example.employeeapi.exception.ConstraintViolationException;
import com.example.employeeapi.model.Department;
import com.example.employeeapi.service.DepartmentService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

@Tag(name = "DEPARTMENT", description = "Department management APIs")
@RestController
@RequestMapping("/api/department")
public class DepartmentController {
    
    private final DepartmentService departmentService;

    @Autowired
    public DepartmentController(DepartmentService departmentService) {
        this.departmentService = departmentService;
    }

    @ExceptionHandler(ConstraintViolationException.class)
    public ResponseEntity<String> handleConstraintViolationException(ConstraintViolationException e) {
        return ResponseEntity.badRequest().body(e.getMessage());
    }

    // 01. 모든 부서 조회
    @Operation(summary = "모든 부서 조회", description = "모든 부서 정보를 조회합니다.", tags = {"조회(GET)"})
    @GetMapping
    public ResponseEntity<List<Department>> getAllDepartment() {
        List<Department> departments = departmentService.getAllDepartments();
        return ResponseEntity.ok(departments);
    }

    // 02. 휴지통 부서 조회
    @Operation(summary = "휴지통 부서 조회", description = "휴지통의 모든 부서 정보를 조회합니다.", tags = {"조회(GET)"})
    @GetMapping("/trash")
    public ResponseEntity<List<Department>> getAllTrashes() {
        List<Department> departments = departmentService.getAllTrashes();
        return ResponseEntity.ok(departments);
    }

    // 03. dnumber로 부서 조회
    @Operation(summary = "특정 dnumber 부서 조회", description = "dnumber값을 통해 특정 부서를 조회합니다.", tags = {"조회(GET)"})
    @GetMapping("/{dnumber}")
    public ResponseEntity<Department> getDepartmentByDnumber(@PathVariable("dnumber") String dnumber) {
        try {
            Department department = departmentService.getDepartmentByDnumber(Integer.parseInt(dnumber));
            return department != null ? ResponseEntity.ok(department) : ResponseEntity.notFound().build();
        } catch (NumberFormatException e) {
            return ResponseEntity.badRequest().body(null);
        }
    }

    // 04. 임의의 특성값으로 부서 조회
    @Operation(summary = "부서 검색", description = "임의의 특성값을 통해 부서를 조회합니다.", tags = {"조회(GET)"})
    @GetMapping("/search")
    public ResponseEntity<?> getDepartmentByAttr(
            @RequestParam(value = "search_attr") List<String> searchAttr,
            @RequestParam(value = "department_value") List<String> departmentValue) {

        if (searchAttr.size() != departmentValue.size()) {
            return ResponseEntity.badRequest().body("not matching parameters");
        }

        List<Object> searchValue = new ArrayList<>();
        for (int i = 0; i < searchAttr.size(); i++) {
            String attr = searchAttr.get(i);
            String value = departmentValue.get(i);
            try {
                switch (attr.toLowerCase()) {
                    case "dname", "mgr_ssn" -> searchValue.add(value);
                    case "mgr_start_date" -> searchValue.add(value);
                    case "dnumber" -> searchValue.add(Integer.parseInt(value));
                    default -> {
                        return ResponseEntity.badRequest().body("Unknown attribute: " + attr);
                    }
                }
            } catch (Exception e) {
                return ResponseEntity.badRequest().body("Invalid value for attribute: " + attr);
            }
        }
        List<Department> departments = departmentService.getDepartmentByAttr(searchAttr, searchValue);
        return departments != null ? ResponseEntity.ok(departments) : ResponseEntity.notFound().build();
    }

    @Operation(summary = "부서 삭제(휴지통)", description = "해당하는 dnumber값의 부서 정보를 휴지통으로 삭제합니다.", tags = {"삭제(DELETE)"})
    @DeleteMapping("/{dnumber}")
    public ResponseEntity<String> soft_deleteDepartmentByDnumber(@PathVariable("dnumber") String dnumber) {
        try {
            boolean isDeleted = departmentService.soft_deleteDepartmentByDnumber(Integer.parseInt(dnumber));
            return isDeleted ? ResponseEntity.ok("Department deleted successfully") : ResponseEntity.notFound().build();
        } catch (NumberFormatException e) {
            return ResponseEntity.badRequest().body(null);
        }
    }

    @Operation(summary = "부서 완전 삭제", description = "해당하는 dnumber값의 부서 정보를 완전히 삭제합니다.", tags = {"삭제(DELETE)"})
    @DeleteMapping("/hard/{dnumber}")
    public ResponseEntity<String> hard_deleteDepartmentByDnumber(@PathVariable("dnumber") String dnumber) {
        try {
            boolean isDeleted = departmentService.hard_deleteDepartmentByDnumber(Integer.parseInt(dnumber));
            return isDeleted ? ResponseEntity.ok("Department deleted successfully") : ResponseEntity.notFound().build();
        } catch (NumberFormatException e) {
            return ResponseEntity.badRequest().body(null);
        }
    }

    @Operation(summary = "부서 복원", description = "해당하는 dnumber값의 부서 정보를 복원합니다.", tags = {"업데이트(PUT)"})
    @PutMapping("/restore/{dnumber}")
    public ResponseEntity<String> restoreDepartmentByDnumber(@PathVariable("dnumber") String dnumber) {
        try {
            boolean isRestored = departmentService.restoreDepartmentByDnumber(Integer.parseInt(dnumber));
            return isRestored ? ResponseEntity.ok("Department restored successfully") : ResponseEntity.notFound().build();
        } catch (NumberFormatException e) {
            return ResponseEntity.badRequest().body(null);
        }
    }

    @Operation(summary = "부서 정보 수정", description = "특정 dnumber값의 부서 정보를 수정합니다.", tags = {"업데이트(PUT)"})
    @PutMapping("/{dnumber}")
    public ResponseEntity<Department> updateDepartmentByDnumber(
            @PathVariable("dnumber") String dnumber,
            @RequestBody Map<String, Object> changeValue) {
        try {
            Department updatedDepartment = departmentService.updateDepartmentByDnumber(Integer.parseInt(dnumber), changeValue);
            return updatedDepartment != null ? ResponseEntity.ok(updatedDepartment) : ResponseEntity.notFound().build();
        } catch (NumberFormatException e) {
            return ResponseEntity.badRequest().body(null);
        }
    }

    @Operation(summary = "부서 추가", description = "해당하는 데이터의 부서를 새로 추가합니다.", tags = {"추가(POST)"})
    @PostMapping
    public ResponseEntity<Department> addDepartment(@RequestBody List<Object> addingValue) {
        Department createdDepartment = departmentService.addDepartment(addingValue);
        return ResponseEntity.status(201).body(createdDepartment);
    }

    @Operation(summary = "부서 통계 정보 조회", description = "해당하는 dnumber값의 부서 통계(AVG/MAX/MIN Salary)를 조회합니다.", tags = {"조회(GET)"})
    @GetMapping("/info/{dnumber}")
    public ResponseEntity<List<Double>> getDepartmentInfo(@PathVariable("dnumber") String dnumber) {
        try {
            List<Double> departmentInfo = departmentService.getDepartmentInfo(Integer.parseInt(dnumber));
            return ResponseEntity.status(201).body(departmentInfo);
        } catch (NumberFormatException e) {
            return ResponseEntity.badRequest().body(null);
        }
    }
}
