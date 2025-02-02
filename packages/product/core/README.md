# Smolblog Core

A library of common code for Smolblog. This is where the core tenants of the platform should be expressed. The code in this repo should adhere to these ideals:

1. Zero dependencies on outside platforms and frameworks.
2. Minimal dependencies on outside libraries, but use where appropriate.
3. All classes should have full test coverage.
4. A class' dependency should be declared as an `interface` wherever possible to allow for future changes.
5. Use documentation comments so that documentation can be generated.

Especially in these early stages, it is assumed that there will be an underlying platform or framework. Translation between these classes and said platform is the responsibility of another project.

By the same token, if a feature would work better as its own library that depends on this library, we should do that.

## Contributing

This is a read-only repository. All development happens in the [Smolblog monorepo](https://github.com/smolblog/smolblog).

## License

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
