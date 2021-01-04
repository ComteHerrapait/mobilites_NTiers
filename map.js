class Partner {
    constructor(id_, locX_, locY_, name_, city_, country_) {
        this.id = id_;
        this.loc_X = locX_;
        this.loc_Y = locY_;
        this.name = name_;
        this.city = city_;
        this.country = country_;
        this.students = [];
    }
    addStudent(student_) {
        this.students.push(student_);
    }
    getLngLat() {
        return [this.loc_X, this.loc_Y];
    }
    getPopupText() {
        let str = `<h3>${this.name}</h3><h5>${this.city}, ${this.country}</h5>`;
        str += "<ul>";
        this.students.forEach(function(s) {
            str += `<li>${s.lname}, ${s.fname}</li>`;
        });
        str += "</ul>";
        return str;
    }
};

class Student {
    constructor(fname_, lname_) {
        this.fname = fname_;
        this.lname = lname_;
    }
};

class MapMarkers {
    constructor(mobilities_) {
        this.partners = [];
        mobilities_.forEach(mobi => this.createMarker(mobi));
    }
    createMarker(mobility_) {
        if (this.partners.some(p => p.id === mobility_[5])) {
            //partner already exists
            let found = this.partners.find(p => p.id === mobility_[5]);
            let s = new Student(mobility_[3], mobility_[4]);
            found.addStudent(s);
        } else {
            let p = new Partner(mobility_[5], mobility_[1], mobility_[2], mobility_[0], mobility_[6], mobility_[7]);
            let s = new Student(mobility_[3], mobility_[4]);
            p.addStudent(s);
            this.partners.push(p);
        }
    }
};